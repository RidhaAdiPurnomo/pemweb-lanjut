const mix = require('laravel-mix');
const path = require('path');

mix.ts('resources/js/app.ts', 'public/js')
    .vue()
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
    ])
    .webpackConfig({
        module: {
            rules: [
                {
                    test: /\.tsx?$/,
                    loader: 'ts-loader',
                    exclude: /node_modules/,
                    options: {
                        appendTsSuffixTo: [/\.vue$/],
                    },
                },
            ],
        },
        resolve: {
            extensions: ['.js', '.jsx', '.ts', '.tsx', '.vue'],
            alias: {
                '@': path.resolve(__dirname, 'resources/js'),
            },
        },
    })
    .version();