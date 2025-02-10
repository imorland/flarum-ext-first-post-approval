import app from 'flarum/forum/app';
import extendIndexPage from './extenders/extendIndexPage';

app.initializers.add('clarkwinkelmann-first-post-approval', () => {
  extendIndexPage();
});
