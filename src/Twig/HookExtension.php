<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Interface\HookInterface;

class HookExtension extends AbstractExtension
{
    private iterable $hooks;

    public function __construct(iterable $hooks)
    {
        $this->hooks = $hooks;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getHook', [$this, 'getHook']),
        ];
    }

    public function getHook(string $name): ?string
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof HookInterface && $hook->getHookName() === $name) {
                return $hook->renderTemplate();
            }
        }
        return null;
    }
}
