<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RouteSelector extends Component
{
    public $name;
    public $label;
    public $value;
    public $required;
    public $multiple;
    public $placeholder;
    public $selectedRoutes;

    /**
     * Create a new component instance.
     *
     * @param string $name
     * @param string $label
     * @param mixed $value
     * @param bool $required
     * @param bool $multiple
     * @param string $placeholder
     * @param array $selectedRoutes
     * @return void
     */
    public function __construct(
        $name = 'routes',
        $label = 'خطوط السير',
        $value = null,
        $required = false,
        $multiple = true,
        $placeholder = 'اختر خطوط السير',
        $selectedRoutes = []
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->required = $required;
        $this->multiple = $multiple;
        $this->placeholder = $placeholder;
        $this->selectedRoutes = $selectedRoutes;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.route-selector');
    }
}
