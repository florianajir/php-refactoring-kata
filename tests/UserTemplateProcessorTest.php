<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../src/Entity/Template.php';
require_once __DIR__.'/../src/Entity/Site.php';
require_once __DIR__.'/../src/Entity/User.php';
require_once __DIR__.'/../src/Helper/SingletonTrait.php';
require_once __DIR__.'/../src/Context/ApplicationContext.php';
require_once __DIR__.'/../src/Processor/TemplateProcessorInterface.php';
require_once __DIR__.'/../src/Processor/UserTemplateProcessor.php';

class UserTemplateProcessorTest extends TestCase
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
        $processor = new UserTemplateProcessor();
        $templateWithoutPlaceholder = new Template(
            1,
            'There is no user placeholder [foo:bar]',
            'No need to call user processor for [baz:qux]'
        );
        $this->assertFalse($processor->isProcessable($templateWithoutPlaceholder));
        $templateWithPlaceholderInContent = new Template(
            1,
            'Placeholder in content',
            'Hello [user:first_name]'
        );
        $this->assertTrue($processor->isProcessable($templateWithPlaceholderInContent));
        $templateWithPlaceholderInSubject = new Template(
            1,
            'Hello [user:first_name]',
            'Placeholder in subject'
        );
        $this->assertTrue($processor->isProcessable($templateWithPlaceholderInSubject));
    }

    /**
     * @test
     */
    public function testProcessWithUserFromContext()
    {
        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();
        $template = new Template(
            1,
            'Inscription confirmée pour [user:email]',
            'Bonjour [user:first_name] [user:last_name]'
        );
        $userTemplateProcessor = new UserTemplateProcessor();
        $message = $userTemplateProcessor->process($template);
        $this->assertEquals(
            sprintf('Inscription confirmée pour %s', mb_strtolower($expectedUser->email)),
            $message->subject
        );
        $this->assertEquals(
            sprintf(
                'Bonjour %s %s',
                ucfirst(mb_strtolower($expectedUser->firstname)),
                ucfirst(mb_strtolower($expectedUser->lastname))
            ),
            $message->content
        );
    }

    /**
     * @test
     */
    public function testProcessWithUserFromData()
    {
        $faker = \Faker\Factory::create();
        $expectedUser = new User($faker->randomNumber(), $faker->firstName(), $faker->lastName, $faker->email);
        $template = new Template(
            1,
            'Inscription confirmée pour [user:email]',
            'Bonjour [user:first_name] [user:last_name]'
        );
        $userTemplateProcessor = new UserTemplateProcessor();
        $message = $userTemplateProcessor->process($template, [
            'user' => $expectedUser,
        ]);
        $this->assertEquals(
            sprintf('Inscription confirmée pour %s', mb_strtolower($expectedUser->email)),
            $message->subject
        );
        $this->assertEquals(
            sprintf(
                'Bonjour %s %s',
                ucfirst(mb_strtolower($expectedUser->firstname)),
                ucfirst(mb_strtolower($expectedUser->lastname))
            ),
            $message->content
        );
    }
}
