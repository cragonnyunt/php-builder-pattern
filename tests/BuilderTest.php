<?php

namespace PHPBuilder\Tests;

use Faker\Factory;
use PHPBuilder\Builder;
use PHPUnit\Framework\TestCase;
use PHPBuilder\Traits\Unbuiltable;
use PHPBuilder\Exceptions\OmitException;
use PHPBuilder\Contracts\RecordInterface;
use PHPBuilder\Contracts\BuilderInterface;
use PHPBuilder\Exceptions\BuilderException;
use PHPBuilder\Exceptions\NoInputException;
use PHPBuilder\Exceptions\TypeMisMatchException;
use PHPBuilder\Exceptions\ForbiddenBuildException;

class BuilderTest extends TestCase
{
    /**
     * The Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    public function testSetter()
    {
        $builder = new class extends Builder
        {
            protected function getObject(): RecordInterface
            {
                return new class ($this) implements RecordInterface
                {
                    public function __construct(BuilderInterface $builder)
                    {
                    }
                };
            }

            protected function checkPrerequisites(): void
            {
            }
        };

        $property = ucfirst($this->faker->word);
        $value = $this->faker->sentence;
        $setMethod = "set$property";
        $getMethod = "get$property";
        $builder->$setMethod($value);

        $this->assertEquals($value, $builder->$getMethod());
    }

    /**
     * @depends testSetter
     */
    public function testSetterWithoutValue()
    {
        $builder = new class extends Builder
        {
            protected function getObject(): RecordInterface
            {
                return new class ($this) implements RecordInterface
                {
                    public function __construct(BuilderInterface $builder)
                    {
                    }
                };
            }

            protected function checkPrerequisites(): void
            {
            }
        };

        $property = ucfirst($this->faker->word);
        $setMethod = "set$property";

        $this->expectException(NoInputException::class);
        $builder->$setMethod();
    }

    /**
     * @depends testSetter
     */
    public function testGetterWithoutSetting()
    {
        $builder = new class extends Builder
        {
            protected function getObject(): RecordInterface
            {
                return new class ($this) implements RecordInterface
                {
                    public function __construct(BuilderInterface $builder)
                    {
                    }
                };
            }

            protected function checkPrerequisites(): void
            {
            }
        };

        $property = ucfirst($this->faker->word);
        $getMethod = "get$property";

        $this->expectException(OmitException::class);
        $builder->$getMethod($property);
    }

    /**
     * @depends testSetter
     */
    public function testBuild()
    {
        $builder = new class extends Builder
        {
            protected function getObject(): RecordInterface
            {
                return new class ($this) implements RecordInterface
                {
                    public function __construct(BuilderInterface $builder)
                    {
                    }
                };
            }

            protected function checkPrerequisites(): void
            {
            }
        };

        $object = $builder->build();

        $this->assertInstanceOf(RecordInterface::class, $object);
    }

    /**
     * @depends testSetter
     */
    public function testHasGetter()
    {
        $builder = new class extends Builder
        {
            protected function getObject(): RecordInterface
            {
                return new class ($this) implements RecordInterface
                {
                    public function __construct(BuilderInterface $builder)
                    {
                    }
                };
            }

            protected function checkPrerequisites(): void
            {
            }
        };

        $property = ucfirst($this->faker->word);
        $value = $this->faker->boolean;
        $setMethod = "set$property";
        $getMethod = "has$property";
        $builder->$setMethod($value);

        $this->assertEquals($value, $builder->$getMethod());
        $this->assertIsBool($builder->$getMethod());

        // throw when non-boolean values
        $this->expectException(TypeMisMatchException::class);

        $property = ucfirst($this->faker->word);
        $value = $this->faker->sentence;
        $setMethod = "set$property";
        $getMethod = "has$property";
        $builder->$setMethod($value);

        $builder->$getMethod();
    }

    /**
     * @depends testSetter
     */
    public function testIsGetter()
    {
        $builder = new class extends Builder
        {
            protected function getObject(): RecordInterface
            {
                return new class ($this) implements RecordInterface
                {
                    public function __construct(BuilderInterface $builder)
                    {
                    }
                };
            }

            protected function checkPrerequisites(): void
            {
            }
        };

        $property = ucfirst($this->faker->word);
        $value = $this->faker->boolean;
        $setMethod = "set$property";
        $getMethod = "is$property";
        $builder->$setMethod($value);

        $this->assertEquals($value, $builder->$getMethod());
        $this->assertIsBool($builder->$getMethod());

        // throw exception for non boolean values
        $this->expectException(TypeMisMatchException::class);
        $property = ucfirst($this->faker->word);
        $value = $this->faker->sentence;
        $setMethod = "set$property";
        $getMethod = "is$property";
        $builder->$setMethod($value);

        $builder->$getMethod();
    }

    /**
     * @depends testBuild
     */
    public function testCheckPrerequisites()
    {
        $property = ucfirst($this->faker->word);

        $builder = new class ($property) extends Builder
        {
            public function __construct(private $property)
            {
                parent::__construct();
            }

            protected function getObject(): RecordInterface
            {
                return new class ($this) implements RecordInterface
                {
                    public function __construct(BuilderInterface $builder)
                    {
                    }
                };
            }

            protected function checkPrerequisites(): void
            {
                if (empty($this->data[$this->property])) {
                    throw new BuilderException('Missing ' . $this->property);
                }
            }
        };

        $this->expectException(BuilderException::class);
        $this->expectExceptionMessage('Missing ' . $property);
        $builder->build();
    }

    /**
     * @depends testBuild
     */
    public function testForbiddenBuilder()
    {
        $builder = new class () extends Builder
        {
            use Unbuiltable;
        };

        $this->expectException(ForbiddenBuildException::class);
        $builder->build();
    }

    /**
     * @depends testSetter
     */
    public function testInvalidMethod()
    {
        $builder = new class extends Builder
        {
            protected function getObject(): RecordInterface
            {
                return new class ($this) implements RecordInterface
                {
                    public function __construct(BuilderInterface $builder)
                    {
                    }
                };
            }

            protected function checkPrerequisites(): void
            {
            }
        };

        $method = ucfirst($this->faker->word);

        $this->expectException(BuilderException::class);
        $builder->$method();
    }

    /**
     * Setup up the Faker instance.
     *
     * @return void
     */
    protected function setUpFaker()
    {
        $this->faker = $this->makeFaker();
    }

    /**
     * Create a Faker instance for the given locale.
     *
     * @param  string  $locale
     * @return \Faker\Generator
     */
    protected function makeFaker($locale = null)
    {
        return Factory::create($locale ?? Factory::DEFAULT_LOCALE);
    }
}
