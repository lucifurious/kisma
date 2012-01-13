<?php

/* bootstrap/_bootstrap_container.page_header.twig */
class __TwigTemplate_ebb54654da6ff609efe631e204e592df extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div class=\"page-header\">
\t<h1>";
        // line 2
        echo twig_escape_filter($this->env, $this->getContext($context, "page_header"), "html", null, true);
        echo "
\t\t<small>";
        // line 3
        echo twig_escape_filter($this->env, $this->getContext($context, "page_header_small"), "html", null, true);
        echo "</small>
\t</h1>
</div>
";
    }

    public function getTemplateName()
    {
        return "bootstrap/_bootstrap_container.page_header.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
