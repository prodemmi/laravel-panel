<template>
  <textarea ref="code" />
</template>

<script>
import CodeMirror from "codemirror";

// Modes
import "codemirror/mode/markdown/markdown";
import "codemirror/mode/javascript/javascript";
import "codemirror/mode/php/php";
import "codemirror/mode/ruby/ruby";
import "codemirror/mode/shell/shell";
import "codemirror/mode/sass/sass";
import "codemirror/mode/yaml/yaml";
import "codemirror/mode/yaml-frontmatter/yaml-frontmatter";
import "codemirror/mode/nginx/nginx";
import "codemirror/mode/xml/xml";
import "codemirror/mode/vue/vue";
import "codemirror/mode/dockerfile/dockerfile";
import "codemirror/mode/sql/sql";
import "codemirror/mode/twig/twig";
import "codemirror/mode/htmlmixed/htmlmixed";

// Thems

import "codemirror/lib/codemirror.css";
import "codemirror/theme/darcula.css";
import "codemirror/theme/3024-day.css";
import "codemirror/theme/3024-night.css";
import "codemirror/theme/abcdef.css";
import "codemirror/theme/ambiance-mobile.css";
import "codemirror/theme/ambiance.css";
import "codemirror/theme/base16-dark.css";
import "codemirror/theme/base16-light.css";
import "codemirror/theme/bespin.css";
import "codemirror/theme/blackboard.css";
import "codemirror/theme/cobalt.css";
import "codemirror/theme/colorforth.css";
import "codemirror/theme/dracula.css";
import "codemirror/theme/duotone-dark.css";
import "codemirror/theme/duotone-light.css";
import "codemirror/theme/eclipse.css";
import "codemirror/theme/elegant.css";
import "codemirror/theme/erlang-dark.css";
import "codemirror/theme/gruvbox-dark.css";
import "codemirror/theme/hopscotch.css";
import "codemirror/theme/icecoder.css";
import "codemirror/theme/idea.css";
import "codemirror/theme/isotope.css";
import "codemirror/theme/lesser-dark.css";
import "codemirror/theme/liquibyte.css";
import "codemirror/theme/lucario.css";
import "codemirror/theme/material.css";
import "codemirror/theme/mbo.css";
import "codemirror/theme/mdn-like.css";
import "codemirror/theme/midnight.css";
import "codemirror/theme/monokai.css";
import "codemirror/theme/neat.css";
import "codemirror/theme/neo.css";
import "codemirror/theme/night.css";
import "codemirror/theme/oceanic-next.css";
import "codemirror/theme/panda-syntax.css";
import "codemirror/theme/paraiso-dark.css";
import "codemirror/theme/paraiso-light.css";
import "codemirror/theme/pastel-on-dark.css";
import "codemirror/theme/railscasts.css";
import "codemirror/theme/rubyblue.css";
import "codemirror/theme/seti.css";
import "codemirror/theme/shadowfox.css";
import "codemirror/theme/solarized.css";
import "codemirror/theme/ssms.css";
import "codemirror/theme/the-matrix.css";
import "codemirror/theme/tomorrow-night-bright.css";
import "codemirror/theme/tomorrow-night-eighties.css";
import "codemirror/theme/ttcn.css";
import "codemirror/theme/twilight.css";
import "codemirror/theme/vibrant-ink.css";
import "codemirror/theme/xq-dark.css";
import "codemirror/theme/xq-light.css";
import "codemirror/theme/yeti.css";
import "codemirror/theme/zenburn.css";

// Addons

import "codemirror/addon/fold/foldcode.js";
import "codemirror/addon/fold/foldgutter.js";
import "codemirror/addon/fold/foldgutter.css";
import "codemirror/addon/search/jump-to-line.js";
import "codemirror/addon/selection/active-line.js";
import "codemirror/addon/fold/brace-fold.js";
import "codemirror/addon/edit/closebrackets.js";
import "codemirror/addon/edit/matchbrackets.js";
import "codemirror/addon/edit/closetag.js";
import "codemirror/addon/search/match-highlighter.js";

// Hints
import "codemirror/addon/hint/show-hint.js";
import "codemirror/addon/hint/show-hint.css";
import "codemirror/addon/hint/anyword-hint.js";
import "codemirror/addon/hint/css-hint.js";
import "codemirror/addon/hint/html-hint.js";
import "codemirror/addon/hint/javascript-hint.js";
import "codemirror/addon/hint/sql-hint.js";
import "codemirror/addon/hint/xml-hint.js";

import { FormMixin } from "../../../mixins";

export default {
  props: ["data", "value", 'extenstion', 'mimeType'],
  mixins: [FormMixin],
  data: () => ({ codemirror: null }),
  mounted() {
    
    this.$nextTick(() => {

      const config = {
        tabSize: 4,
        indentWithTabs: true,
        lineWrapping: true,
        lineNumbers: true,
        theme: this.data?.theme || 'darcula',
        viewportMargin: Infinity,
        mode: this.data?.mode || ((this.extenstion || this.mimeType) ? this.detectMode() : null),
        readOnly: this.data?.readonly || false,
        autoCloseBrackets: true,
        matchBrackets: true,
        matchTags: true,
        autoCloseTags: true,
        foldGutter: true,
        styleActiveLine: true,
        styleActiveSelected: true,
        highlightSelectionMatches: {
          minChars: 2,
          showToken: /\w/,
          style: "matchhighlight",
          annotateScrollbar: true,
        },
        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
        extraKeys: {"Ctrl-Space": "autocomplete"},
        ...this.data?.options
      };

      this.codemirror = CodeMirror.fromTextArea(this.$refs.code, config);

      let doc = this.codemirror.getDoc()

      doc.on("change", (code, changeObj) => {
          this.model = code.getValue();
          this.onChange(this.model);
      });

      doc.setValue(this.value);
    
    })

  },
  methods: {
    detectMode() {
      return CodeMirror.findModeByExtension(this.extenstion)?.mode || CodeMirror.findModeByMIME(this.mimeType)?.mode || null
    }
  }
};
</script>

<style>
.CodeMirror {
  font: 14px/1.5 Menlo, Consolas, Monaco, "Andale Mono", monospace;
  box-sizing: border-box;
  z-index: 0;
  width: 100%;
  min-height: 60vh;
}

.CodeMirror-scroll {
  overflow: auto;
  box-sizing: border-box;
}
</style>
