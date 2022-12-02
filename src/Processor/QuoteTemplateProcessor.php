<?php

/**
 * Process templates with [quote:*] placeholders
 */
class QuoteTemplateProcessor implements TemplateProcessorInterface
{
    /**
     * Check in the template subject & content if contains [quote:*] placeholders
     */
    public function isProcessable(Template $template): bool
    {
        return 1 === preg_match('/\[quote:.*]/', $template->subject.$template->content);
    }

    /**
     * Substitute Template placeholders [quote:*] in subject & content props
     */
    public function process(Template $template, ?array $data = null): Template
    {
        if (null !== $quote = $this->getQuoteFromData($data)) {
            $this->computeText($template->subject, $quote);
            $this->computeText($template->content, $quote);
        }

        return $template;
    }

    private function getQuoteFromData(?array $data): ?Quote
    {
        return isset($data['quote']) && $data['quote'] instanceof Quote
            ? $data['quote']
            : null;
    }

    /**
     * [quote:*] placeholders dictionary key => value
     */
    private function computeText(string &$text, Quote $quote): void
    {
        $substitutes = [
            // add new [quote:*] placeholders here
            '[quote:summary_html]' => $quote->renderHtml(),
            '[quote:summary]' => $quote->renderText(),
            '[quote:destination_name]' => DestinationRepository::getInstance()
                ->getById($quote->destinationId)
                ->countryName,
            '[quote:destination_link]' => sprintf(
                '%s/%s/quote/%s',
                SiteRepository::getInstance()->getById($quote->siteId)->url,
                DestinationRepository::getInstance()->getById($quote->destinationId)->countryName,
                $quote->id
            ),
        ];

        $text = strtr($text, $substitutes);
    }
}