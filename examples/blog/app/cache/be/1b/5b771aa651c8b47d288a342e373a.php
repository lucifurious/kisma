<?php

/* _error.twig */
class __TwigTemplate_be1b5b771aa651c8b47d288a342e373a extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'all_content' => array($this, 'block_all_content'),
            'page_scripts' => array($this, 'block_page_scripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "bootstrap/_bootstrap_container_one_column.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_all_content($context, array $blocks = array())
    {
        // line 4
        echo "
<h2 style=\"color: #888;\">";
        // line 5
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "error"), "type"), "html", null, true);
        echo "</h2>

<h4>
\t<div  style=\"font-style: oblique;\">";
        // line 8
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "error"), "message"), "html", null, true);
        echo "</div>
\t<span style=\"font-size: 80%; float: right;\"><a href=\"#dump\">Dump</a> | <a href=\"#trace\">Trace</a> </span>
</h4>

";
        // line 12
        if ($this->getAttribute($this->getContext($context, "error"), "source")) {
            // line 13
            echo "<div style=\"margin-top: 25px;\">
\t<h2 style=\"color: #888;\"><a name=\"dump\" style=\"text-decoration: none;\">Source Dump</a></h2>

\t<div>";
            // line 16
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "error"), "file"), "html", null, true);
            echo " (";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "error"), "line"), "html", null, true);
            echo ")</div>
\t<script type=\"syntaxhighlighter\"
\t\tclass=\"pad-line-numbers: true; unindent: true; brush: php; toolbar: false; first-line: ";
            // line 18
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "error"), "start_line"), "html", null, true);
            echo "; highlight: [";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "error"), "line"), "html", null, true);
            echo "];\">
\t\t<![CDATA[
\t\t";
            // line 20
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "error"), "source"), "html", null, true);
            echo "
\t\t]]>
\t</script>
</div>
";
        }
        // line 25
        echo "
";
        // line 26
        if ($this->getAttribute($this->getContext($context, "error"), "trace")) {
            // line 27
            echo "<div style=\"margin-top: 25px;\">
\t<h2 style=\"color: #888;\"><a name=\"trace\" style=\"text-decoration: none;\">Backtrace</a></h2>

\t<div style=\"padding-left: 20px;padding-right:20px;\">
\t\t<table class=\"condensed-table zebra-striped\">
\t\t\t<tr>
\t\t\t\t<th>When</th>
\t\t\t\t<th>Where</th>
\t\t\t</tr>
\t\t\t";
            // line 36
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, "error"), "trace"));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 37
                echo "\t\t\t<tr>
\t\t\t\t<td style=\"text-align: right;\">";
                // line 38
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "item"), "index"), "html", null, true);
                echo "</td>
\t\t\t\t<td><strong>";
                // line 39
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "item"), "function"), "html", null, true);
                echo "</strong>
\t\t\t\t<br /><span style=\"font-size: 80%;\">";
                // line 40
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "item"), "file_name"), "html", null, true);
                echo " (<em>Line ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, "item"), "line"), "html", null, true);
                echo "</em>)</span></td>
\t\t\t</tr>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 43
            echo "\t\t</table>
\t</div>
</div>
";
        }
        // line 47
        echo "
";
    }

    // line 50
    public function block_page_scripts($context, array $blocks = array())
    {
        // line 51
        echo "<script type=\"text/javascript\">
\t\$(function() {
\t\tSyntaxHighlighter.config.stripBrs = true;
\t\tSyntaxHighlighter.all();
\t});
</script>";
    }

    public function getTemplateName()
    {
        return "_error.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
