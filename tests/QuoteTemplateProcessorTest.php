<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../src/Entity/Destination.php';
require_once __DIR__.'/../src/Entity/Quote.php';
require_once __DIR__.'/../src/Entity/Template.php';
require_once __DIR__.'/../src/Helper/SingletonTrait.php';
require_once __DIR__.'/../src/Context/ApplicationContext.php';
require_once __DIR__.'/../src/Repository/Repository.php';
require_once __DIR__.'/../src/Repository/DestinationRepository.php';
require_once __DIR__.'/../src/Repository/QuoteRepository.php';
require_once __DIR__.'/../src/Processor/TemplateProcessorInterface.php';
require_once __DIR__.'/../src/Processor/QuoteTemplateProcessor.php';

class QuoteTemplateProcessorTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    /**
     * @test
     */
    public function testisProcessable()
    {
        $processor = new QuoteTemplateProcessor();
        $templateWithoutPlaceholder = new Template(
            1,
            'There is no quote placeholder [foo:bar]',
            'No need to call quote processor for [baz:qux]'
        );
        $this->assertFalse($processor->isProcessable($templateWithoutPlaceholder));
        $templateWithPlaceholderInContent = new Template(
            1,
            'With placeholder in content',
            '[quote:summary_html]'
        );
        $this->assertTrue($processor->isProcessable($templateWithPlaceholderInContent));
        $templateWithPlaceholderInSubject = new Template(
            1,
            'Going to [quote:destination_name]',
            'The placeholder is in subject'
        );
        $this->assertTrue($processor->isProcessable($templateWithPlaceholderInSubject));
    }

    /**
     * @test
     */
    public function testProcess()
    {
        $faker = \Faker\Factory::create();
        $destinationId = $faker->randomNumber();
        $expectedDestination = DestinationRepository::getInstance()->getById($destinationId);
        $quote = new Quote($faker->randomNumber(), $faker->randomNumber(), $destinationId, $faker->date());
        $template = new Template(
            1,
            'Going to [quote:destination_name]',
            '[quote:summary_html]'
        );
        $userTemplateProcessor = new QuoteTemplateProcessor();
        $message = $userTemplateProcessor->process($template, [
            'quote' => $quote,
        ]);
        $this->assertEquals(
            "Going to $expectedDestination->countryName",
            $message->subject
        );
        $this->assertEquals(
            $quote->renderHtml(),
            $message->content
        );
    }
}
