<?php

class CommandHandlerContainer
{
    public function __construct(array $commandHandlers)
    {
        $this->commandHandlers = $commandHandlers;
    }

    public function get(string $commandId): CommandHandler
    {
        if (!isset($this->commandHandlers[$commandId])) {
            throw new Exception(sprintf("Unknown command '%s'", $commandId)); // TODO
        }

        return $this->commandHandlers[$commandId](); 
    }
}

class ConvertToCommand
{
    public function __invoke(Request $request): Either // Either<CommandFailure,Command>
    {
        $maybeAction = $request->get('action');
        $maybeParamaters = $request->get('parameters');

        if ($maybeAction->isNothing() || $maybeParamaters->isNothing()) {
            return Either::left(CommandFailure::fromString('Invalid Request'));
        }

        return Either::right(Command::from($maybeAction->get(), $maybeParamaters->get()));
    }
}

class ExecuteCommand
{
    public function __construct(CommandHandlerContainer $commandHandlerContainer)
    {
        $this->commandHandlerContainer = $commandHandlerContainer;
    }

    public function __invoke(Command $command): Either // Either<CommandFailure,CommandSuccess>
    {
        try {
            $commandHandler = $this->commandHandlerContainer->get($command->action());
        } catch (Exception $e) {
            return Either::left(CommandFailure::fromString(sprintf("Unknown command '%s'", $command->action())));
        }

        return $commandHandler->execute($command);
    }
}

class Application
{
    public function __construct(CommandHandlerContainer $commandHandlerContainer)
    {
        $this->commandHandlerContainer = $commandHandlerContainer;
    }

    public function execute(Request $request): Response
    {
        $pipeline = (new Pipeline)
            ->pipe(new ConvertToCommand())
            ->pipe(new ExecuteCommand($this->commandHandlerContainer));

        return $pipeline
            ->process($request)
            ->either(
                fn(CommandSuccess $success) => Response::fromArray(['result' => 'success', 'details' => $success->toString()]),
                fn(CommandFailure $failure) => Response::fromArray(['result' => 'error', 'details' => $failure->toString()])
            );
    }
}
