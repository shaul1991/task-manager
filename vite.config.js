import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/layout.js',
                'resources/js/tasks/taskForm.js',
                'resources/js/tasks/taskSlideOver.js',
                'resources/js/tasks/quickAddTask.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
