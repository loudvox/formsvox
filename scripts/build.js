const esbuild = require('esbuild');

esbuild.build({
  entryPoints: ['src/index.tsx'],
  bundle: true,
  outfile: 'build/index.js',
  loader: { '.tsx': 'tsx', '.ts': 'ts' },
  jsx: 'transform',
  external: ['@wordpress/element', '@wordpress/components', '@wordpress/data', '@wordpress/i18n', 'react', 'react-dom'],
}).then(() => {
  console.log('Build complete!');
}).catch((err) => {
  console.error(err);
  process.exit(1);
});
