import { extend } from 'flarum/common/extend';
import ItemList from 'flarum/common/utils/ItemList';
import app from 'flarum/forum/app';
import IndexPage from 'flarum/forum/components/IndexPage';
import Button from 'flarum/components/Button';
import DateFilterModal from "./components/DateFilterModal";

app.initializers.add('annonny/flarum-date-filter', () => {
  extend(IndexPage.prototype, 'viewItems', function (items: ItemList) {
    if (app.current.data.routeName === 'byobuPrivate') return;
    let classNameForDateFilter = "Date-Filter";
    if (app.search.params().q && app.search.params().q.split(' ').filter((e) => {
      return (e.substr(0, 8) == "created:") && (e != '')
    }).join('')!=""){
      classNameForDateFilter = "Date-Filter-Dated"
    }
    items.add("DateFilter", Button.component({
      icon: 'fas fa-calendar',
      className: 'Button Button--icon Date-Filter',
      id: classNameForDateFilter,
      onclick: () => {
        app.modal.show(DateFilterModal)
      }
    }),50);
  });
});
