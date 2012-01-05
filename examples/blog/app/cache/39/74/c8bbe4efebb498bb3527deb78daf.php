<?php

/* layouts/_master.html_head.twig */
class __TwigTemplate_3974c8bbe4efebb498bb3527deb78daf extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<head>
\t<meta charset=\"utf-8\">
\t<title>";
        // line 3
        echo twig_escape_filter($this->env, $this->getContext($context, "page_title"), "html", null, true);
        echo "</title>

\t<!-- Scripts -->
\t<script src=\"http://html5shim.googlecode.com/svn/trunk/html5.js\" type=\"text/javascript\"></script>
\t<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>

\t";
        // line 9
        if ($this->getContext($context, "error")) {
            // line 10
            echo "\t<script type=\"text/javascript\" src=\"/vendor/SyntaxHighlighter/scripts/XRegExp.js\"></script>
\t<script type=\"text/javascript\" src=\"/vendor/SyntaxHighlighter/scripts/shCore.js\"></script>
\t<script type=\"text/javascript\" src=\"/vendor/SyntaxHighlighter/scripts/shBrushJScript.js\"></script>
\t<script type=\"text/javascript\" src=\"/vendor/SyntaxHighlighter/scripts/shBrushPhp.js\"></script>
\t<script type=\"text/javascript\" src=\"/vendor/SyntaxHighlighter/scripts/shBrushPlain.js\"></script>

\t<link href=\"/vendor/SyntaxHighlighter/styles/shCore.css\" type=\"text/css\" rel=\"stylesheet\" />
\t<link href=\"/vendor/SyntaxHighlighter/styles/shThemeEclipse.css\" type=\"text/css\" rel=\"stylesheet\" />
\t";
        }
        // line 19
        echo "
\t<!-- Styles-->
\t<link href=\"/css/bootstrap/bootstrap.css\" rel=\"stylesheet\">

\t<!-- In-line Styles -->
\t<style type=\"text/css\">
\t\t\t/* Override some defaults */
\t\thtml, body {
\t\t\tbackground-color: #eee;
\t\t}

\t\tbody {
\t\t\tpadding-top: 40px; /* 40px to make the container go all the way to the bottom of the topbar */
\t\t}

\t\t.container > footer p {
\t\t\ttext-align: center; /* center align it with the container */
\t\t}

\t\t.container {
\t\t\twidth: 820px;
\t\t\t/* downsize our container to make the content feel a bit tighter and more cohesive. NOTE:
\t\t\t\t\t\t this removes two full columns from the grid, meaning you only go to 14 columns and not 16. */
\t\t}

\t\t\t/* The white background content wrapper */
\t\t.content {
\t\t\tbackground-color: #fff;
\t\t\tpadding: 20px;
\t\t\tmargin: 0 -20px; /* negative indent the amount of the padding to maintain the grid system */
\t\t\t-webkit-border-radius: 0 0 6px 6px;
\t\t\t-moz-border-radius: 0 0 6px 6px;
\t\t\tborder-radius: 0 0 6px 6px;
\t\t\t-webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, .15);
\t\t\t-moz-box-shadow: 0 1px 2px rgba(0, 0, 0, .15);
\t\t\tbox-shadow: 0 1px 2px rgba(0, 0, 0, .15);
\t\t}

\t\t\t/* Page header tweaks */
\t\t.page-header {
\t\t\tbackground-color: #f5f5f5;
\t\t\tpadding: 20px 20px 10px;
\t\t\tmargin: -20px -20px 20px;
\t\t}

\t\t\t/* Styles you shouldn't keep as they are for displaying this base example only */
\t\t.content .span10,
\t\t.content .span4 {
\t\t\tmin-height: 500px;
\t\t}

\t\t\t/* Give a quick and non-cross-browser friendly divider */
\t\t.content .span4 {
\t\t\tmargin-left: 0;
\t\t\tpadding-left: 19px;
\t\t\tborder-left: 1px solid #eee;
\t\t}

\t\t.topbar .btn {
\t\t\tborder: 0;
\t\t}

\t\t";
        // line 81
        if ($this->getContext($context, "error")) {
            // line 82
            echo "\t\tdiv.syntaxhighlighter table tbody tr td.code div.container:after {
\t\t\tclear: none !important;
\t\t}

\t\tdiv.syntaxhighlighter td .container:before, .container:after {
\t\t\tdisplay: block;
\t\t}
\t\t";
        }
        // line 90
        echo "
\t</style>

</head>";
    }

    public function getTemplateName()
    {
        return "layouts/_master.html_head.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
