<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../src/Entity/Site.php';
require_once __DIR__.'/../src/Entity/Template.php';
require_once __DIR__.'/../src/Helper/SingletonTrait.php';
require_once __DIR__.'/../src/Context/ApplicationContext.php';
require_once __DIR__.'/../src/Repository/Repository.php';
require_once __DIR__.'/../src/Repository/SiteRepository.php';
require_once __DIR__.'/../src/Processor/TemplateProcessorInterface.php';
require_once __DIR__.'/../src/Processor/SiteTemplateProcessor.php';
require_once __DIR__.'/../src/TemplateManager.php';

class SiteTemplateProcessorTest extends TestCase
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
        $processor = new SiteTemplateProcessor();
        $templateWithoutPlaceholder = new Template(
            1,
            'There is no user placeholder [foo:bar]',
            'No need to call user processor for [baz:qux]'
        );
        $this->assertFalse($processor->isProcessable($templateWithoutPlaceholder));
        $templateWithPlaceholderInContent = new Template(
            1,
            'Placeholder in content',
            'Visit [site:url]'
        );
        $this->assertTrue($processor->isProcessable($templateWithPlaceholderInContent));
        $templateWithPlaceholderInSubject = new Template(
            1,
            'Welcome on [site:id]',
            'Placeholder in subject'
        );
        $this->assertTrue($processor->isProcessable($templateWithPlaceholderInSubject));
    }

    /**
     * @test
     */
    public function testProcessWithSiteFromContext()
    {
        $currentSite = ApplicationContext::getInstance()->getCurrentSite();
        $template = new Template(
            1,
            'Inscription confirmée sur [site:id]',
            'Lien: [site:url]'
        );
        $userTemplateProcessor = new SiteTemplateProcessor();
        $message = $userTemplateProcessor->process($template);
        $this->assertEquals(
            "Inscription confirmée sur $currentSite->id",
            $message->subject
        );
        $this->assertEquals(
            "Lien: $currentSite->url",
            $message->content
        );
    }

    /**
     * @test
     */
    public function testProcessWithSiteFromData()
    {
        $faker = \Faker\Factory::create();
        $site = new Site($faker->randomNumber(), $faker->url);
        $template = new Template(
            1,
            'Inscription confirmée sur [site:id]',
            'Lien: [site:url]'
        );
        $siteTemplateProcessor = new SiteTemplateProcessor();
        $message = $siteTemplateProcessor->process($template, [
            'site' => $site,
        ]);
        $this->assertEquals(
            "Inscription confirmée sur $site->id",
            $message->subject
        );
        $this->assertEquals(
            "Lien: $site->url",
            $message->content
        );
    }
}
