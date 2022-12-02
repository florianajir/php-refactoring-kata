<?php

class TemplateManager
{
    /**
     * @var TemplateProcessorInterface[]
     */
    private $processors;

    /**
     * @param TemplateProcessorInterface[] $processors
     */
    public function __construct(array $processors = null)
    {
        // no BC tweak for new DI, might be optimized by looking up for TemplateProcessorInterface implementations
        $this->processors = $processors ?? [
            new UserTemplateProcessor(),
            new QuoteTemplateProcessor(),
        ];
    }

    public function getTemplateComputed(Template $tpl, array $data)
    {
        $message = clone $tpl;
        foreach ($this->processors as $processor) {
            if ($processor->isProcessable($message)) {
                $processor->process($message, $data);
            }
        }

        return $message;
    }
}
