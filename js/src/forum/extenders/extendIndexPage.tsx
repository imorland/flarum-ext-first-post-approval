import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import IndexPage from 'flarum/forum/components/IndexPage';
import ItemList from 'flarum/common/utils/ItemList';
import type Mithril from 'mithril';

export default function extendIndexPage() {
  extend(IndexPage.prototype, 'sidebarItems', function (items: ItemList<Mithril.Children>) {
    if (app.initializers.has('fof-byobu') && app.current.get('routeName') === 'byobuPrivate') {
      if (items.has('newDiscussion') && !app.forum.attribute<boolean>('canStartPrivateDiscussion')) {
        items.remove('newDiscussion');
      }
    }
  });
}
