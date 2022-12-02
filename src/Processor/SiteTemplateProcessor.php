<?php

/**
 * Process templates with [site:*] placeholders
 */
class SiteTemplateProcessor implements TemplateProcessorInterface
{
    public function isProcessable(Template $template): bool
    {
        return 1 === preg_match('/\[site:.*]/', $template->subject.$template->content);
    }

    /**
     * Substitute template placeholders [site:*] with data from context or via parameter
     */
    public function process(Template $template, ?array $data = null): Template
    {
        $site = $this->getSiteData($data);
        $this->computeText($template->subject, $site);
        $this->computeText($template->content, $site);

        return $template;
    }

    /**
     * Get site from data array if available or from ApplicationContext otherwise
     */
    private function getSiteData(?array $data): Site
    {
        return isset($data['site']) && $data['site'] instanceof Site
            ? $data['site']
            : ApplicationContext::getInstance()->getCurrentSite();
    }

    private function computeText(string &$text, Site $site): void
    {
        $substitutes = [
            '[site:id]' => $site->id,
            '[site:url]' => $site->url,
        ];
        $text = strtr($text, $substitutes);
    }
}