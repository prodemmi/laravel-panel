import FileManagerTool from './FileManagerTool.vue'
import VueSimpleContextMenu from 'vue-simple-context-menu';
import 'vue-simple-context-menu/dist/vue-simple-context-menu.css';

Lava.addTool({
    path: '/tools/file-manager',
    name: 'tool',
    component: FileManagerTool
})

Lava.addComponent('vue-simple-context-menu', VueSimpleContextMenu);