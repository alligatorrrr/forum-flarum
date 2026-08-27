import app from 'flarum/forum/app';
import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';
import ItemList from 'flarum/common/utils/ItemList';
import Stream from 'flarum/utils/Stream';
import flatpickr from 'flatpickr'

export default class DateFilterModal extends Modal {

  oninit(vnode) {
    super.oninit(vnode);
    const custommid = "";
    const startDate = ""
    let endDate = "";
    if (app.search.params().q) endDate = app.search.params().q.split(' ').filter((e) => {
        return (e.substr(0, 8) == "created:") && (e != '')
      }).join('').replace("created:","")
    this.custommid = Stream(custommid);
    this.endDate = Stream(endDate);
  }

  className() {
    return 'Modal--small';
  }

  title() {
    return "日期筛选";
  }

  content() {
    const fields = this.fields().toArray();
    return (
      <div className="Modal-body">
        <div className="Form">{this.fields().toArray()}</div>
      </div>
    );
  }


  fields() {
    const items = new ItemList();

    items.add(
      'mid',
      <div className="Date-Filter-Form-group">
        <div className="FilterModal--date" oncreate={this.configDatePicker.bind(this)}>
          <input style="opacity: 1; color: inherit" className="FormControl" data-input/>
          {Button.component({
            className: 'Button Button--icon DateFilterModal--button',
            icon: 'fas fa-times',
            'data-clear': true,
          })}
        </div>
      </div>
    );

    items.add(
      'submit',
      <div className="Form-group">
        {Button.component(
          {
            className: 'Button Button--primary',
            type: 'submit',
            loading: this.loading,
          },
          "确定"
        )}
      </div>,
      -10
    );

    return items;
  }

  configDatePicker(vnode) {
    flatpickr(vnode.dom, {
      minDate: '2022-07-01',
      maxDate: 'today',
      dateFormat: 'Y-m-d',
      altFormat: 'Y-m-d',
      defaultDate: this.endDate().split(".."),
      wrap: true,
      mode: 'range',
      onChange: (dates, dateString, datess) => {
        this.endDate(dateString.replace(" to ", ".."));
      },
    });
  }

  onsubmit(e) {
    e.preventDefault();
    const params = app.search.params();
    const old = params.q;
    if (!this.endDate||this.endDate()=="") {
      if (params.q){
        params.q = params.q.split(' ').filter((e) => {
          return (e.substr(0, 8) != "created:") && (e != '')
        }).join(' ')
        if (params.q[0] == ' ') params.q = params.q.substr(1)
        if (params.q[params.q.length - 1] == ' ') params.q = params.q.substr(0, params.q.length - 1)
        m.route.set(app.route(app.current.get('routeName'), {...m.route.param(), ...params}));
      }
      this.hide();
      return;
    } else {
      if (params.q){
      params.q = params.q.split(' ').filter((e) => {
        return (e.substr(0, 8) != "created:") && (e != '')
      }).join(' ')
      if (params.q[0] == ' ') params.q = params.q.substr(1)
      if (params.q[params.q.length - 1] == ' ') params.q = params.q.substr(0, params.q.length - 1)
      if (params.q.length > 0) {
        params.q += ' created:' + this.endDate();
      } else {
        params.q += 'created:' + this.endDate();
      }}else{
        params.q = 'created:' + this.endDate();
      }
    }

    this.loading = true;
    if (params.q&&old !== params.q) {
      m.route.set(app.route(app.current.get('routeName'), {...m.route.param(), ...params}));
    }else{this.hide();return;}
  }

}
