<?php

/* layouts/_master.page_header.twig */
class __TwigTemplate_8727e701f80cd3d41563711786a1261f extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div class=\"page-header\">
\t<h1>";
        // line 2
        if (array_key_exists("page_header", $context)) {
            echo twig_escape_filter($this->env, $this->getContext($context, "page_header"), "html", null, true);
        }
        // line 3
        echo "\t\t<small>";
        if (array_key_exists("page_header_small", $context)) {
            echo twig_escape_filter($this->env, $this->getContext($context, "page_header_small"), "html", null, true);
        }
        echo "</small>
\t</h1>
</div>
";
    }

    public function getTemplateName()
    {
        return "layouts/_master.page_header.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
