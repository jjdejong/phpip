import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            //'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});


// Bootstrap's recommendations
// const path = require('path')
// export default defineConfig({
//     root: path.resolve(__dirname, 'src'),
//     plugins: [
//         laravel({
//             input: [
//                 'resources/js/app.js',
//             ],
//             refresh: true,
//         }),
//     ],
//     resolve: {
//         alias: {
//           '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
//         }
//       },    
// });
