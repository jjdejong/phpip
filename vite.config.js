import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
//import inject from '@rollup/plugin-inject';

export default defineConfig({
    plugins: [
        // inject({
        //     //$: ['jquery', '$'],
        //     jQuery: ['jquery', 'jQuery'],
        //     bootstrap: ['bootstrap', '*'],
        // }),
        laravel({
            input: [
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            //'jQuery': '$',
        }
    },
});
