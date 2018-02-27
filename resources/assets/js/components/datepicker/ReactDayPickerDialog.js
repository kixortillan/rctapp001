import React from 'react'
import { MDCDialog } from '@material/dialog'
import DayPicker from 'react-day-picker'
import 'react-day-picker/lib/style.css'
import './datepicker.css'

const currentYear = new Date().getFullYear();
const fromMonth = new Date(currentYear - 10, 0);
const toMonth = new Date(currentYear + 10, 11);

class ReactDayPickerDialog extends React.Component {

    constructor(props) {

        super(props)
        this.state = {

            dialog: null,
            show: false,
            month: fromMonth,
            year: currentYear,

        }

        this.handleYearMonthChange = this.handleYearMonthChange.bind(this)
    }

    handleYearMonthChange(month) {
        
        this.setState({ month })

    }

    componentDidMount() {

        let dialog = new MDCDialog(document.getElementById(this.props.id))

        dialog.listen('MDCDialog:cancel', () => {
            dialog.close()
            this.props.onClose()
            this.setState({ show: false })
        })

        this.setState({ dialog: dialog })
    }

    componentWillReceiveProps(nextProps) {

        const { show } = this.state

        if (show !== nextProps.show) {
            this.setState({ show: nextProps.show })
        }

    }

    render() {

        const { dialog, show } = this.state
        const { id, ...rest } = this.props

        if (show) {

            dialog.show()

        }

        const modifiersStyles = {


        }

        return (

            <aside id={id} className="mdc-dialog mdc-theme--dark"
                role="alertdialog"
                aria-labelledby="mdc-dialog-with-list-label"
                aria-describedby="mdc-dialog-with-list-description"
                tabIndex="1">
                <div className="mdc-dialog__surface" style={{width: 'auto', minWidth: 'auto'}}>
                    <header className="mdc-dialog__header">
                        <h2 id="mdc-dialog-with-list-label" className="mdc-dialog__header__title">
                        Choose Date
                        </h2>
                        <i className="material-icons mdc-dialog__footer__button--cancel" 
                        style={{cursor: 'pointer'}}>
                        close
                        </i>
                    </header>
                    <section id="mdc-dialog-with-list-description" 
                    className="mdc-dialog__body" 
                    style={{minHeight: '312px'}}>
                        <DayPicker 
                        {...rest}
                        month={this.state.month}
                        year={this.state.year}
                        fromMonth={fromMonth}
                        toMonth={toMonth}
                        captionElement={({date, localeUtils}) => 
                            (<YearMonthForm date={date} 
                            localeUtils={localeUtils} 
                            onChange={this.handleYearMonthChange}/>)} />
                    </section>
                </div>
                <div className="mdc-dialog__backdrop"></div>
            </aside>

        )

    }

}

function YearMonthForm({ date, localeUtils, onChange }) {
    const months = localeUtils.getMonths();

    const years = [];
    for (let i = fromMonth.getFullYear(); i <= toMonth.getFullYear(); i += 1) {
        years.push(i);
    }

    const handleChange = function handleChange(e) {
        const { year, month } = e.target.form;
        onChange(new Date(year.value, month.value));
    };

    return (
        <form className="DayPicker-Caption">
            <select name="month" onChange={handleChange} value={date.getMonth()} 
            style={{marginRight: '5px'}}>
            {months.map((month, i) => (
                <option key={month} value={i}>
                {month}
                </option>
            ))}
            </select>
            <select name="year" onChange={handleChange} value={date.getFullYear()}>
            {years.map(year => (
                <option key={year} value={year} selected={year == currentYear}>
                {year}
                </option>
            ))}
            </select>
        </form>
    );
}

export default ReactDayPickerDialog