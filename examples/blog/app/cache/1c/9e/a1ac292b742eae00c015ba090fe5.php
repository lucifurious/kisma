<?php

/* blog/index.twig */
class __TwigTemplate_1c9ea1ac292b742eae00c015ba090fe5 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'all_content' => array($this, 'block_all_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layouts/_master_one_column.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        $context["page_header"] = "Page header";
        // line 4
        $context["page_header_small"] = "Page header small";
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 6
    public function block_all_content($context, array $blocks = array())
    {
        // line 7
        echo "<h1>Welcome to the Blog Example!</h1>
";
    }

    public function getTemplateName()
    {
        return "blog/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
