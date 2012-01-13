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
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_all_content($context, array $blocks = array())
    {
        // line 4
        echo "<h1>Welcome to the Blog Example!</h1>
<div id=\"posts\">
\t";
        // line 6
        if (($this->getAttribute($this->getContext($context, "blogs", true), "results", array(), "any", true, true) && (twig_length_filter($this->env, $this->getAttribute($this->getContext($context, "blogs"), "results")) > 0))) {
            // line 7
            echo "\t";
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, "blogs"), "results"));
            foreach ($context['_seq'] as $context["_key"] => $context["post"]) {
                // line 8
                echo "\t\tID: ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "post"), "id", array(), "array"), "html", null, true);
                echo " :: ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "post"), "seq", array(), "array"), "html", null, true);
                echo "<BR/>
\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['post'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 10
            echo "\t";
        }
        // line 11
        echo "</div>
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
