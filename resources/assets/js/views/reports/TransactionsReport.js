import React from 'react'
import {withRouter} from 'react-router-dom'
import QueryString from 'query-string'
import FileSaver from 'file-saver'
import {MDCTextField} from '@material/textfield'
import {MDCSelect} from '@material/select'
import {MDCDialog} from '@material/dialog'
import {MDCRadio} from '@material/radio'

import Toolbar from '../../components/Toolbar'
import ReactDayPickerDialog from '../../components/datepicker/ReactDayPickerDialog'
import MessageDialog from '../../components/MessageDialog'
import LinearProgress from '../../components/LinearProgress'

import DataTable from '../../components/datatable/DataTable'
import DataRow from '../../components/datatable/DataRow'
import DataCell from '../../components/datatable/DataCell'

import {isEmpty} from '../../utilities/helpers'

class TransactionsReport extends React.Component {

    constructor(props) {

        super(props)

        //data Reports manages
        this.state = {
            
            //parameters
            mode: 'yearly',
            year: '',
            year_monthly: '',
            month_from: '',
            month_to: '',
            date_from: '',
            date_to: '',

            //transaction details
            transactions: [],
            //subtotal per column, subtotal per row
            transaction_meta: {},

            //data loaded on filters
            filters: {
                modes: [],
                years: [],
                months: [],
            },

            //flag for disabling yearly filter
            yearly_filter_disabled: false,

            //flag for disabling monthly filter
            monthly_filter_disabled: true,

            //flag for disabling date range filter
            date_range_filter_disabled: true,

            //flag for disabling download button
            is_download_ready: false,

            message: '',

            show_message: false,

            errors: {
                date_from: '',
                date_to: '',
            },

            show_date_from_dialog: false,
            show_date_to_dialog: false,

            loading: false,

        }

        this.initFilters = this.initFilters.bind(this)
        this.buildQueryString = this.buildQueryString.bind(this)

        //event functions
        this.handleDownload = this.handleDownload.bind(this)
        this.handleSearch = this.handleSearch.bind(this)
        this.handleChangeMode = this.handleChangeMode.bind(this)
        this.handleChangeYear = this.handleChangeYear.bind(this)
        this.handleChangeYearMonthly = this.handleChangeYearMonthly.bind(this)
        this.handleChangeFromMonth = this.handleChangeFromMonth.bind(this)
        this.handleChangeToMonth = this.handleChangeToMonth.bind(this)
        this.handleSelectDateFrom = this.handleSelectDateFrom.bind(this)
        this.handleShowDateFromCalendar = this.handleShowDateFromCalendar.bind(this)
        this.handleOnCloseDateFromCalendar = this.handleOnCloseDateFromCalendar.bind(this)
        this.handleSelectDateTo = this.handleSelectDateTo.bind(this)
        this.handleShowDateToCalendar = this.handleShowDateToCalendar.bind(this)
        this.handleOnCloseDateToCalendar = this.handleOnCloseDateToCalendar.bind(this)

        this.handleOnCloseMessage = this.handleOnCloseMessage.bind(this)

    }

    handleDownload(event) {

        this.setState({loading: true})

        axios.get('/api/reports/transactions/download?' + QueryString.stringify(this.buildQueryString()), {
                responseType: 'blob'
        })
        .then((resp) => {

            FileSaver.saveAs(resp.data, 'transaction_report.xls')

        }).catch((err) => {
            
            console.log(err)

        }).then(() => {

            this.setState({loading: false})

        })
            
    }

    handleSearch(event) {

        event.preventDefault()
        event.stopPropagation()

        this.setState({loading: true})

        axios.get('/api/reports/transactions?' + QueryString.stringify(this.buildQueryString()))
            .then((resp) => {
            
                this.setState({
                    transactions: resp.data.data, 
                    transaction_meta: resp.data.meta,
                    is_download_ready: true,
                })


            }).catch((err) => {

                //console.log(err)
                if(err.response){
                    if(err.response.status === 422){
                        this.setState({show_message: true, message: err.response.data.error})
                    }
                }

            }).then(() => {

                this.setState({loading: false})

            })

    }

    handleChangeMode(event) {

        switch(event.target.value) {
            
            case "monthly":
                this.setState({
                    monthly_filter_disabled: false, 
                    yearly_filter_disabled: true, 
                    date_range_filter_disabled: true,
                    mode: "monthly",
                })
                document.querySelector('#wrap_date_from').classList.add('mdc-text-field--disabled')
                document.querySelector('#wrap_date_to').classList.add('mdc-text-field--disabled')
                break;

            case "date_range":
                this.setState({
                    monthly_filter_disabled: true, 
                    yearly_filter_disabled: true,
                    date_range_filter_disabled: false,
                    mode: "date_range",
                })
                document.querySelector('#wrap_date_from').classList.remove('mdc-text-field--disabled')
                document.querySelector('#wrap_date_to').classList.remove('mdc-text-field--disabled')
                break;

            default:
            case "yearly":
                this.setState({
                    monthly_filter_disabled: true, 
                    yearly_filter_disabled: false, 
                    date_range_filter_disabled: true,
                    mode: "yearly",
                })
                document.querySelector('#wrap_date_from').classList.add('mdc-text-field--disabled')
                document.querySelector('#wrap_date_to').classList.add('mdc-text-field--disabled')
                break;

        }

    }

    handleChangeYear(event) {

        let val = event.target.options[event.target.selectedIndex].value 
        this.setState({year: val})

    }

    handleChangeYearMonthly(event) {

        let val = event.target.options[event.target.selectedIndex].value 
        this.setState({year_monthly: val})

    }

    handleChangeFromMonth(event) {

        let val = event.target.options[event.target.selectedIndex].value 
        this.setState({month_from: val})

    }

    handleChangeToMonth(event) {

        let val = event.target.options[event.target.selectedIndex].value 
        this.setState({month_to: val})

    }

    handleSelectDateFrom(date, mods, event) {

        this.setState({date_from: date, show_date_from_dialog: false})

    }

    handleShowDateFromCalendar(event) {

        this.setState({show_date_from_dialog: true})

    }

    handleOnCloseDateFromCalendar() {

        this.setState({show_date_from_dialog: false})

    }

    handleSelectDateTo(date, mods, event) {

        this.setState({date_to: date, show_date_to_dialog: false})

    }

    handleShowDateToCalendar(event) {

        this.setState({show_date_to_dialog: true})

    }

    handleOnCloseDateToCalendar() {

        this.setState({show_date_to_dialog: false})

    }

    handleOnCloseMessage() {
        
        this.setState({show_message: false})

    }

    buildQueryString() {

        const {
            mode, year, year_monthly, 
            month_from, month_to, 
            date_from, date_to} = this.state

        var dateFrom = moment(date_from).format('Y-MM-DD')
        var dateTo = moment(date_to).format('Y-MM-DD')

        switch(mode){

            case 'monthly':
                return {
                    mode: mode,
                    year_monthly: year_monthly,
                    month_from: month_from,
                    month_to: month_to,
                }

            case 'date_range':
                return {
                    mode: mode,
                    date_from: dateFrom,
                    date_to: dateTo,
                }

            case 'yearly':
            default: 
                return {
                    mode: mode,
                    year: year,
                }

        }

    }

    initFilters() {

        const filters = localStorage.getObject('filters')
        
        this.setState({
            mode: filters.reports.default_mode,
            year: filters.current_year,
            year_monthly: filters.current_year,
            month_to: filters.current_month - 1,
            month_from: filters.current_month - 1,
            filters: {
                modes: filters.reports.modes,
                years: filters.years,
                months: filters.months_in_year,
            },
        })

    }

    componentDidMount() {

        this.initFilters()
        const textField1 = new MDCTextField(document.getElementById('wrap_date_from'));
        const textField2 = new MDCTextField(document.getElementById('wrap_date_to'));

    }

    render() {
    
        const {transactions, transaction_meta, filters, loading} = this.state

        return (
                
                <section>

                    <LinearProgress show={loading} />

                    <div className="mdc-layout-grid">

                        <div className="mdc-layout-grid__inner">

                            <div className="mdc-layout-grid__cell--span-12">
                                
                                    <div className="box">

                                        <section className="box-content">

                                            <div className="mdc-layout-grid__inner">

                                                <div className="mdc-layout-grid__cell--span-12">

                                                    <div className="mdc-form-field">
                                                        <div className="mdc-radio">
                                                            <input onChange={this.handleChangeMode} className="mdc-radio__native-control" type="radio" id="radio-yearly" value="yearly" name="mode" checked={this.state.mode === 'yearly'} />
                                                            <div className="mdc-radio__background">
                                                                <div className="mdc-radio__outer-circle"></div>
                                                                <div className="mdc-radio__inner-circle"></div>
                                                            </div>
                                                        </div>
                                                        <label id="radio-yearly-label" htmlFor="radio-yearly">Yearly</label>
                                                    </div>

                                                </div>

                                                <div className="mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-2-tablet mdc-layout-grid__cell--span-12-phone">

                                                    <div className="mdc-select fullwidth" disabled={this.state.yearly_filter_disabled}>
                                                        <select className="mdc-select__surface" onChange={this.handleChangeYear}>
                                                            {filters.years.map(($val, $key) => {
                                                                return <option key={'yr1_' + $key} value={$val} role="menuitem" tabIndex="0">{$val}</option>
                                                            })}
                                                        </select>
                                                        <div className="mdc-select__bottom-line"></div>
                                                    </div>
                                                                               
                                                </div>

                                                <div className="mdc-layout-grid__cell--span-12">

                                                    <div className="mdc-form-field">
                                                        <div className="mdc-radio">
                                                            <input onChange={this.handleChangeMode} className="mdc-radio__native-control" type="radio" id="radio-monthly" value="monthly" name="mode" checked={this.state.mode === 'monthly'} />
                                                            <div className="mdc-radio__background">
                                                                <div className="mdc-radio__outer-circle"></div>
                                                                <div className="mdc-radio__inner-circle"></div>
                                                            </div>
                                                        </div>
                                                        <label id="radio-monthly-label" htmlFor="radio-monthly">Monthly</label>
                                                    </div>

                                                </div>

                                                <div className="mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-2-tablet mdc-layout-grid__cell--span-12-phone">

                                                    <div className="mdc-select fullwidth" disabled={this.state.monthly_filter_disabled}>
                                                        <select className="mdc-select__surface" onChange={this.handleChangeYearMonthly}>
                                                            {filters.years.map(($val, $key) => {
                                                                return <option key={'yr2_' + $key} value={$val} role="menuitem" tabIndex="0">{$val}</option>
                                                            })}
                                                        </select>
                                                        <div className="mdc-select__bottom-line"></div>
                                                    </div>
                                                                               
                                                </div>

                                                <div className="mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-12-phone">

                                                    <div className="mdc-select fullwidth" disabled={this.state.monthly_filter_disabled}>
                                                        <select className="mdc-select__surface" onChange={this.handleChangeFromMonth}>
                                                            {filters.months.map(($val, $key) => {
                                                                return <option key={'mn1_' + $key} value={$key} role="menuitem" tabIndex="0">{$val}</option>
                                                            })}
                                                        </select>
                                                        <div className="mdc-select__bottom-line"></div>
                                                    </div>
                                                                               
                                                </div>

                                                <div className="mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-3-tablet mdc-layout-grid__cell--span-12-phone">

                                                    <div className="mdc-select fullwidth" disabled={this.state.monthly_filter_disabled}>
                                                        <select className="mdc-select__surface" onChange={this.handleChangeToMonth}>
                                                            {filters.months.map(($val, $key) => {
                                                                return <option key={'mn2_' + $key} value={$key} role="menuitem" tabIndex="0">{$val}</option>
                                                            })}
                                                        </select>
                                                        <div className="mdc-select__bottom-line"></div>
                                                    </div>
                                                                               
                                                </div>

                                                <div className="mdc-layout-grid__cell--span-12">
                                                    
                                                    <div className="mdc-form-field">
                                                        <div className="mdc-radio">
                                                            <input onChange={this.handleChangeMode} className="mdc-radio__native-control" type="radio" id="radio-date-range" value="date_range" name="mode" checked={this.state.mode === 'date_range'} />
                                                            <div className="mdc-radio__background">
                                                                <div className="mdc-radio__outer-circle"></div>
                                                                <div className="mdc-radio__inner-circle"></div>
                                                            </div>
                                                        </div>
                                                        <label id="radio-date-range-label" htmlFor="radio-date-range">Date Range</label>
                                                    </div>

                                                </div>

                                                <div className="mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-12-phone">

                                                    <div id="wrap_date_from" onClick={this.handleShowDateFromCalendar} className="mdc-text-field mdc-text-field--dense mdc-text-field--with-leading-icon mdc-text-field--disabled fullwidth">
                                                        <i className="material-icons mdc-text-field__icon" tabIndex="0">date_range</i>
                                                        
                                                        <input id="txt_date_from" name="txt_date_from" value={!isEmpty(this.state.date_from) ? moment(this.state.date_from).format('Y-MM-DD') : ''} style={{height: '100%', paddingBottom: '0'}} className="mdc-text-field__input" type="text" placeholder="From" />
                                                        
                                                        <div className="mdc-text-field__bottom-line"></div>
                                                    </div>
                                                    <p id="txt_date_from-validation-msg" className="mdc-text-field-helper-text" aria-hidden="true"></p>

                                                    <ReactDayPickerDialog id="dialog_date_from" 
                                                    show={this.state.show_date_from_dialog} 
                                                    onClose={this.handleOnCloseDateFromCalendar} 
                                                    selectedDays={!isEmpty(this.state.date_from) ? this.state.date_from : {}} 
                                                    onDayClick={this.handleSelectDateFrom} />
                                                
                                                </div>

                                                <div className="mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-12-phone">

                                                    <div id="wrap_date_to" onClick={this.handleShowDateToCalendar} className="mdc-text-field mdc-text-field--dense mdc-text-field--with-leading-icon mdc-text-field--disabled fullwidth">
                                                        <i className="material-icons mdc-text-field__icon" tabIndex="0">date_range</i>
                                                        
                                                        <input id="txt_date_to" value={!isEmpty(this.state.date_to) ? moment(this.state.date_to).format('Y-MM-DD') : ''} style={{height: '100%', paddingBottom: '0'}} className="mdc-text-field__input" type="text" placeholder="To" />
                                                        
                                                        <div className="mdc-text-field__bottom-line"></div>
                                                    </div>
                                                    <p id="txt_date_to-validation-msg" className="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg mdc-text-field-helper-text--persistent" aria-hidden="true"></p>
                                                    
                                                    <ReactDayPickerDialog id="dialog_date_to" 
                                                    show={this.state.show_date_to_dialog} 
                                                    onClose={this.handleOnCloseDateToCalendar} 
                                                    selectedDays={!isEmpty(this.state.date_to) ? this.state.date_to : {}} 
                                                    onDayClick={this.handleSelectDateTo} />

                                                </div>

                                                <div className="mdc-layout-grid__cell--span-10-desktop
                                                mdc-layout-grid__cell--span-4-tablet
                                                mdc-layout-grid__cell--span-12-phone">
                                                </div>

                                                <div className="mdc-layout-grid__cell--span-1-desktop
                                                mdc-layout-grid__cell--span-2-tablet
                                                mdc-layout-grid__cell--span-12-phone">

                                                    <button onClick={this.handleDownload} 
                                                    disabled={!this.state.is_download_ready} 
                                                    className="mdc-button mdc-button--raised 
                                                    mdc-button--dense mdc-button--compact fullwidth" 
                                                    type="button" aria-label="Download">
                                                        <i className="material-icons mdc-button__icon">
                                                        file_download
                                                        </i>
                                                        Download
                                                    </button>

                                                </div>

                                                <div className="mdc-layout-grid__cell--span-1-desktop
                                                mdc-layout-grid__cell--span-2-tablet
                                                mdc-layout-grid__cell--span-12-phone">

                                                    <button onClick={this.handleSearch} 
                                                    className="mdc-button mdc-button--raised 
                                                    mdc-button--dense mdc-button--compact fullwidth" 
                                                    type="button" aria-label="Search">
                                                        <i className="material-icons mdc-button__icon">
                                                        search
                                                        </i>
                                                        Search
                                                    </button>

                                                </div>                                                

                                            </div>

                                        </section>

                                    </div>

                            </div>

                            <div className="mdc-layout-grid__cell--span-12
                            mdc-layout-grid__cell--span-8-tablet
                            mdc-layout-grid__cell--span-4-phone"
                            style={{overflowX: 'auto'}}>

                                <DataTable 
                                headers={transaction_meta.headers}
                                paginate={false}>
                                    {transactions.length > 0 && creatTableBody(transactions, transaction_meta)}
                                </DataTable>

                            </div>

                        </div>

                    </div>

                    <MessageDialog id="dialog_message" 
                    show={this.state.show_message} 
                    title="Transaction Report" 
                    message={this.state.message} 
                    onClose={this.handleOnCloseMessage} />

                </section>

        )

    }

}

const creatTableBody = (transactions, transactionsMeta) => {
    
    if(transactions.length < 1){
        
        return null

    }

    var table = []

    transactions.map((record, index) => {
        let tempTotal = 0

        table.push(<DataRow key={record.code + '_' + index}>
            <DataCell type="text" data={record.code} />
                
            {record.logs.map((log, index) => {

                tempTotal += log.count
                return <DataCell key={record.code + new Date().getTime() + index} type="number" data={log.count} />

            })}
            
            <DataCell type="number" data={tempTotal} />
        </DataRow>)
    })

    table.push(<DataRow key="subtotal">
        <DataCell type="text" data="Total" />
        {transactionsMeta.subtotal_per_month.map((t, i) => {
            return <DataCell key={'subtotal_' + i} type="number" data={t.subtotal} />
        })}
        <DataCell type="number" data={transactionsMeta.grand_total} />
    </DataRow>)

    return table

}

export default withRouter(TransactionsReport)