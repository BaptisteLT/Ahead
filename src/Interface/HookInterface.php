<?php
namespace App\Interface;


interface HookInterface{
    /**
     * The name used to retrieve the Hook in Twig
     * 
     * For exemple if the Hook function getHookName() returns "productHook"
     * Then you will retrieve your Hook in twig using {{ getHook('productHook')|raw }}
     */
    public function getHookName(): ?string;

    public function renderTemplate(): string;
}