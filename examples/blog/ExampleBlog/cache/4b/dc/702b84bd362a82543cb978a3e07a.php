<?php

/* bootstrap/_bootstrap_container.topbar.twig */
class __TwigTemplate_4bdc702b84bd362a82543cb978a3e07a extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div class=\"topbar\">
\t<div class=\"fill\">
\t\t<div class=\"container\">
\t\t\t<a class=\"brand\" href=\"#\">";
        // line 4
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "topbar"), "brand"), "html", null, true);
        echo "</a>
\t\t\t";
        // line 5
        if ((twig_length_filter($this->env, $this->getAttribute($this->getContext($context, "topbar"), "items")) > 0)) {
            // line 6
            echo "\t\t\t<ul class=\"nav\">
\t\t\t\t";
            // line 7
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, "topbar"), "items"));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 8
                echo "\t\t\t\t<li class=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "item"), "active"), "html", null, true);
                echo "\"><a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "item"), "href"), "html", null, true);
                echo "\"
\t\t\t\t\t";
                // line 9
                if ($this->getAttribute($this->getContext($context, "item"), "target")) {
                    echo "target=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "item"), "target"), "html", null, true);
                    echo "\"";
                }
                echo ">";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "item"), "title"), "html", null, true);
                echo "</a></li>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 11
            echo "\t\t\t</ul>
\t\t\t";
        }
        // line 13
        echo "\t\t</div>
\t</div>
</div>";
    }

    public function getTemplateName()
    {
        return "bootstrap/_bootstrap_container.topbar.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
