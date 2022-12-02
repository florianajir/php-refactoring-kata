<?php

/**
 * This interface define specs for Template processing (string replacements) according to domains
 *
 * @see QuoteTemplateProcessor for [quote:*] templating
 * @see UserTemplateProcessor for [user:*] templating
 * @see SiteTemplateProcessor for [site:*] templating
 */
interface TemplateProcessorInterface
{
    /**
     * Check if the processor need to be executed
     * Eg: looking for placeholders pattern in template or context related conditions
     */
    public function isProcessable(Template $template): bool;

    /**
     * Process template placeholders substitution from given data
     */
    public function process(Template $template, ?array $data = null): Template;
}