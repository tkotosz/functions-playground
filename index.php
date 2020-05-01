<?php

require __DIR__ . '/maybe-either-pipeline.php';
require __DIR__ . '/app-models.php';
require __DIR__ . '/app.php';

class Product
{
    public static function fromValues(string $id, string $name): Product
    {
        return new self($id, $name);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    private function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}

class ProductRepository
{
    private $products = [];

    public function nextIndentity(): string
    {
        return uniqid();
    }

    public function save(Product $product): void
    {
        if (rand(0, 1) === 1) { // simulate db failure
            throw new \Exception('DB error 1234');
        }

        $this->products[] = $product;
    }
}

class CreateProductCommand
{
    private array $parameters;

    public static function fromArray(array $parameters): CreateProductCommand
    {
        if (!isset($parameters['name'])) {
            throw new \InvalidArgumentException('Product name is required!');
        }

        return new self($parameters);
    }

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function withId(string $id): CreateProductCommand
    {
        return self::fromArray(['id' => $id] + $this->parameters);
    }

    public function id(): string
    {
        return $this->parameters['id'];
    }

    public function name(): string
    {
        return $this->parameters['name'];
    }

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }
}

class ConvertToCreateProductCommand
{
    public function __invoke(Command $command): Either
    {
        try {
            return Either::right(CreateProductCommand::fromArray($command->parameters()));
        } catch (Exception $e) {
            return Either::left($e->getMessage());
        }
    }
}

class AllocateProductId
{
    public function __invoke(CreateProductCommand $command): Either
    {
        return Either::right($command->withId(uniqid()));
    }
}

class ConvertToProduct
{
    public function __invoke(CreateProductCommand $command): Either
    {
        try {
            return Either::right(Product::fromValues($command->id(), $command->name()));
        } catch (Exception $e) {
            return Either::left($e->getMessage());
        }
    }
}

class PersistProduct
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function __invoke(Product $product): Either
    {
        try {
            $this->productRepository->save($product);
            return Either::right($product->id());
        } catch (Exception $e) {
            return Either::left($e->getMessage());
        }
    }
}

class CreateProductCommandHandler implements CommandHandler
{
    public function execute(Command $command): Either // Either<CommandFailure,CommandSuccess>
    {
        $pipeline = (new Pipeline)
            ->pipe(new ConvertToCreateProductCommand())
            ->pipe(new AllocateProductId())
            ->pipe(new ConvertToProduct())
            ->pipe(new PersistProduct(new ProductRepository()));

        return $pipeline
            ->process($command)
            ->either(
                fn($productId) => Either::right(CommandSuccess::fromString('Product saved successfully, ID: ' . $productId)),
                fn($errorMessage) => Either::left(CommandFailure::fromString('An error occurred: ' . $errorMessage))
            );
    }
}

$input = '{
    "action": "create_product",
    "parameters": {
        "name": "Test Product 1"
    }
}';

$commandHandlers = new CommandHandlerContainer(
    [
        Command::ACTION_CREATE_PRODUCT => fn() => new CreateProductCommandHandler()
    ]
);

$app = new Application($commandHandlers);
$request = Request::fromArray(json_decode($input, true));
$response = $app->execute($request);
echo json_encode($response->toArray()) . PHP_EOL;
