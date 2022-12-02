<?php

/**
 * Process templates with [user:*] placeholders
 */
class UserTemplateProcessor implements TemplateProcessorInterface
{
    public function isProcessable(Template $template): bool
    {
        return 1 === preg_match('/\[user:.*]/', $template->subject.$template->content);
    }

    /**
     * Substitute Template placeholders [*:*] by given data when available
     */
    public function process(Template $template, ?array $data = null): Template
    {
        $user = $this->getUserData($data);
        $this->computeText($template->subject, $user);
        $this->computeText($template->content, $user);

        return $template;
    }

    /**
     * Get user from data array if available or from ApplicationContext otherwise
     */
    private function getUserData(?array $data): User
    {
        return isset($data['user']) && $data['user'] instanceof User
            ? $data['user']
            : ApplicationContext::getInstance()->getCurrentUser();
    }

    /**
     * Substitute text placeholders related to [user:*]
     */
    private function computeText(string &$text, User $user): void
    {
        $substitutes = [
            '[user:first_name]' => ucfirst(mb_strtolower($user->firstname)),
            '[user:last_name]' => ucfirst(mb_strtolower($user->lastname)),
            '[user:email]' => mb_strtolower($user->email),
        ];
        $text = strtr($text, $substitutes);
    }
}