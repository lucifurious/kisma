<?php

/* bootstrap/_bootstrap_container.page_footer.twig */
class __TwigTemplate_76f44bf9d0c242206eca29a566bb9bbb extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'footer_content' => array($this, 'block_footer_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<footer>
\t";
        // line 2
        $this->displayBlock('footer_content', $context, $blocks);
        // line 7
        echo "</footer>
";
    }

    // line 2
    public function block_footer_content($context, array $blocks = array())
    {
        // line 3
        echo "\t\t<div>
\t\t\t<span class=\"pull-left\">Page rendered at: ";
        // line 4
        echo twig_escape_filter($this->env, $this->getContext($context, "page_date"), "html", null, true);
        echo "</span><span class=\"pull-right\">";
        echo twig_escape_filter($this->env, $this->getContext($context, "app_version"), "html", null, true);
        echo "</span>
\t\t</div>
\t";
    }

    public function getTemplateName()
    {
        return "bootstrap/_bootstrap_container.page_footer.twig";
    }

}
