import React from 'react'
import {withRouter} from 'react-router-dom'
import QueryString from 'query-string'
import {MDCSimpleMenu} from '@material/menu'
import C3Chart from 'react-c3js'
import 'c3/c3.css'
import { LineChart, Line, XAxis, YAxis, Tooltip, ResponsiveContainer } from 'recharts'


import JQueryKnob from '../components/JQueryKnob'
//import LineGraph from '../components/LineGraph'
import MessageDialog from '../components/MessageDialog'
import LinearProgress from '../components/LinearProgress'
import Alert from '../components/Alert'

class Dashboard extends React.Component{
    
    constructor(props) {

        super(props)

        //data Dashboard manages
        this.state = {

            //query string for calling api's
            query: {},

            //toggle popup dialog
            show_message: false,

            //message to show at dialog
            message: '',

            //data rendered in knobs
            knobs: [],

            //instance of Chartjs
            linegraph: null,

            //
            graph: {

                line: {
                    data: {
                        x: 'x',
                        columns: [
                            //['x', 'Jan', 'Feb', 'Mar', 'Apr'],
                            //['download', 20, 40, 60],
                            //['Loan and Benefits', 90, 100, 140, 200],
                        ],
                        type: 'line',
                    },
                    colors: {},
                },

            },

            //config for linegraph
            lineopts: {
                
                responsive: true,

                maintainAspectRatio: false,

                onResize: function(chart) {

                    

                },

                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            suggestedMin: 0,
                            maxTicksLimit: 5,
                        },
                        gridLines: {
                            color: "#9E9E9E"
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false,
                        },
                    }],
                },

                elements: {
                    point: {
                        radius: 0,
                    },
                    line: {
                        borderWidth: 2,
                        tension: 0, // disables bezier curves
                        fill: false, // removes fill color under line
                    }
                },

                legend: {
                    labels: {
                        // This more specific font property overrides the global property
                        fontColor: '#000',
                        fontSize: 14,
                        fontFamily: 'Roboto',
                    }
                },

                title: {
                  display: true,
                  text: 'Inquiry Graph',
                },

                hover: {
                    animationDuration: 0, // duration of animations when hovering an item
                },

            },

            //cards
            counters: [],

            //data displayed by filter menu's
            menus: {
                modes: [],
                years: [],
                months: [],
                days: [],
                current_year: null,
                current_month: null,
                current_day: null,
                default_mode: null,
            },

            css: {

                filters: {

                    show_years: 'hidden',

                    show_months: 'hidden',

                    show_days: 'hidden',

                }

            },

            alert: {

                show: false,
                type: '',
                text: '',

            },

            loading: false,

        }//end of state


        this.bindToInstance()
    }

    bindToInstance() {

        this.handleModeSelected = this.handleModeSelected.bind(this)
        this.handleYearSelected = this.handleYearSelected.bind(this)
        this.handleMonthSelected = this.handleMonthSelected.bind(this)
        this.handleDaySelected = this.handleDaySelected.bind(this)
        this.handleMonth2Selected = this.handleMonth2Selected.bind(this)
        this.handleDay2Selected = this.handleDay2Selected.bind(this)
        this.handleFilterClicked = this.handleFilterClicked.bind(this)
        this.handleUpdateDashboard = this.handleUpdateDashboard.bind(this)
        this.handleOnCloseMessage = this.handleOnCloseMessage.bind(this)
        this.isDateRangeValid = this.isDateRangeValid.bind(this)

    }

    handleUpdateDashboard(qs) {

        let append = null

        if(qs !== null && typeof qs === 'object'){
            if(qs.mode == 'yearly'){
                append = {
                    mode: qs.mode,
                    year: qs.year,
                }
            }else if(qs.mode == 'monthly'){
                append = {
                    mode: qs.mode,
                    year: qs.year,
                    month: qs.month,
                }
            }else if(qs.mode == 'range'){
                append = {
                    mode: qs.mode,
                    year: qs.year,
                    month: qs.month,
                    day: qs.day,
                    month2: qs.month2,
                    day2: qs.day2,
                }
            }else{
                append = {
                    mode: qs.mode,
                }
            }
        }

        this.setState({loading: true})

        axios.all([
            axios.get('api/dashboard/stats/knobs?' + QueryString.stringify(append)),
            axios.get('api/dashboard/stats/linegraph?' + QueryString.stringify(append)),
        ]).then(axios.spread((knobs, line) => {

            var data = []
            var colors = {}

            data.push(['x'].concat(line.data.x_axis))

            line.data.y_axis.forEach((i) => {
                data.push([i.id].concat(i.data))
                colors[i.id] = i.color
            })

            // //update linegraph data
            // let linedata = {
            //     labels: line.data.x_axis,
            //     datasets: line.data.y_axis,
            // }

            // if(this.state.linegraph){
            //     this.state.linegraph.destroy()
            // }

            // const ctx = document.getElementById("line_dashboard")
            // const chart = new Chart(ctx, {
                        
            //     type: 'line',
            //     data: linedata,
            //     options: this.state.lineopts,

            // })

            const {graph} = this.state

            graph.line.data.columns = data
            graph.line.colors = colors
            console.log(graph)
            //update data
            this.setState({ 
                graph: graph,
                knobs: knobs.data.knobs ,
                counters: knobs.data.loans_and_benefits_application
            })

            for(let i = 0; i < knobs.data.knobs.length; i++) {
                document.getElementById('knobs_' + i).value = knobs.data.knobs[i].percent
                document.getElementById('knobs_' + i).dispatchEvent(new Event('change'))
            }

        })).catch((err) => {

            console.log(err)

        }).then(() => {

            this.setState({loading: false})

        })

    }

    handleModeSelected(e) {

        const mode = e.detail.item.textContent.trim()
        let query = this.state.query
        
        if(mode == 'yearly'){
            document.getElementById('btn_year').disabled = false
            document.getElementById('btn_month').disabled = true
            document.getElementById('btn_day').disabled = true
            document.getElementById('btn_month2').disabled = true
            document.getElementById('btn_day2').disabled = true
        } else if(mode == 'monthly') {
            document.getElementById('btn_year').disabled = false
            document.getElementById('btn_month').disabled = false
            document.getElementById('btn_day').disabled = true
            document.getElementById('btn_month2').disabled = true
            document.getElementById('btn_day2').disabled = true
        } else if (mode == 'range') {
            document.getElementById('btn_year').disabled = false
            document.getElementById('btn_month').disabled = false
            document.getElementById('btn_day').disabled = false
            document.getElementById('btn_month2').disabled = false
            document.getElementById('btn_day2').disabled = false
        } else{
            document.getElementById('btn_year').disabled = true
            document.getElementById('btn_month').disabled = true
            document.getElementById('btn_day').disabled = true
            document.getElementById('btn_month2').disabled = true
            document.getElementById('btn_day2').disabled = true
        }

        query.mode = mode

        this.setState({ 
            
            query: query,
            menus: { ...this.state.menus, default_mode: mode}

        })

    }

    handleYearSelected(e) {

        const year = e.detail.item.textContent.trim()
        const query = this.state.query
        query.year = year
        this.setState({ 
            
            query: query,
            menus: { ...this.state.menus, current_year: year}

        })
        
    }

    handleMonthSelected(e) {

        const month = e.detail.index + 1
        const query = this.state.query
        query.month = month
        this.setState({ 
            
            query: query,
            menus: { ...this.state.menus, current_month: month}

        })
        

    }

    handleDaySelected(e) {
        
        const day = e.detail.item.textContent.trim()
        const query = this.state.query
        query.day = day
        this.setState({ 
            
            query: query,
            menus: { ...this.state.menus, current_day: day}

        })
        

    }

    handleMonth2Selected(e) {

        const month = e.detail.index + 1
        const query = this.state.query
        query.month2 = month
        this.setState({ 
            
            query: query,
            menus: { ...this.state.menus, current_month: month}

        })
        

    }

    handleDay2Selected(e) {
        
        const day = e.detail.item.textContent.trim()
        const query = this.state.query
        query.day2 = day
        this.setState({ 
            
            query: query,
            menus: { ...this.state.menus, current_day: day}

        })
        

    }

    handleFilterClicked() {

        const qs = this.state.query

        if(qs.mode === 'range' && !this.isDateRangeValid()){
            return;
        }

        this.handleUpdateDashboard(qs)

    }

    handleOnCloseMessage(){

        this.setState({show_message: false})

    }

    isDateRangeValid() {

        const {query} = this.state
        
        if(query.month2 < query.month || query.day2 < query.day){
            this.setState({
                alert: {
                    show: true,
                    type: 'error',
                    text: 'Please choose a valid date range.'
                }
            })

            setTimeout(() => {
                this.setState({
                    alert: {
                        show: false,
                    }
                })
            }, 3000)

            return false
        }

        return true
    }

    initMenus() {
        
        var filters = localStorage.getObject('filters')

        this.setState({ 

            query: {
                year: filters.current_year,
                month: filters.current_month,
                day: filters.current_day,
                month2: filters.current_month,
                day2: filters.current_day,
                mode: filters.dashboard.default_mode,
            },

            menus: {

                modes: filters.dashboard.modes,
                years: filters.years,
                months: filters.short_months_in_year,
                days: filters.days,

            } 
        })
        

        let menuMode = new MDCSimpleMenu(document.getElementById('menu_mode'))
        document.getElementById('btn_mode').addEventListener('click', () => menuMode.open = !menuMode.open)

        let menuYear = new MDCSimpleMenu(document.getElementById('menu_year'))
        document.getElementById('btn_year').addEventListener('click', () => menuYear.open = !menuYear.open)

        let menuMonth = new MDCSimpleMenu(document.getElementById('menu_month'))
        document.getElementById('btn_month').addEventListener('click', () => menuMonth.open = !menuMonth.open)

        let menuDay = new MDCSimpleMenu(document.getElementById('menu_day'))
        document.getElementById('btn_day').addEventListener('click', () => menuDay.open = !menuDay.open)

        let menuMonth2 = new MDCSimpleMenu(document.getElementById('menu_month2'))
        document.getElementById('btn_month2').addEventListener('click', () => menuMonth2.open = !menuMonth2.open)

        let menuDay2 = new MDCSimpleMenu(document.getElementById('menu_day2'))
        document.getElementById('btn_day2').addEventListener('click', () => menuDay2.open = !menuDay2.open)

    }

    componentWillMount() {
        
        this.setState({ query: QueryString.parse(location.hash) })

    }

    componentDidMount() {

        const {query} = this.state

        this.initMenus()

        this.handleUpdateDashboard(query)

        document.getElementById('menu_mode')
            .addEventListener('MDCSimpleMenu:selected', this.handleModeSelected)
        document.getElementById('menu_year')
            .addEventListener('MDCSimpleMenu:selected', this.handleYearSelected)
        document.getElementById('menu_month')
            .addEventListener('MDCSimpleMenu:selected', this.handleMonthSelected)
        document.getElementById('menu_day')
            .addEventListener('MDCSimpleMenu:selected', this.handleDaySelected)
        document.getElementById('menu_month2')
            .addEventListener('MDCSimpleMenu:selected', this.handleMonth2Selected)
        document.getElementById('menu_day2')
            .addEventListener('MDCSimpleMenu:selected', this.handleDay2Selected)

        this.props.onChangeModule('Dashboard')

    }

    render() {

        var month
        var month2

        if(this.state.query.month){
            month = moment().month(this.state.query.month)
                .subtract(1, 'month').format('MMM')
        }

        if(this.state.query.month2){
            month2 = moment().month(this.state.query.month2)
                .subtract(1, 'month').format('MMM')
        }
        
        return (

            <section>

                <LinearProgress show={this.state.loading} />

                <Alert show={this.state.alert.show}
                type={this.state.alert.type} text={this.state.alert.text} />

                <div className="mdc-layout-grid">

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-5-desktop hide-on-tablet hide-on-phone" />

                        <div className="mdc-layout-grid__cell--span-1-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">

                            <div className="menu-filters mdc-menu-anchor">
                                <button id="btn_mode" className="mdc-button mdc-button--raised mdc-button--dense mdc-button--compact">
                                {this.state.query.mode}
                                <i className="material-icons mdc-button__icon">arrow_drop_down</i>
                                </button>
                                <div id="menu_mode" className="mdc-simple-menu" tabIndex="-1" style={{maxHeight: '300px'}}>
                                    <ul className="mdc-simple-menu__items mdc-list" role="menu" aria-hidden="true">
                                        {this.state.menus.modes.map((d, i) => {
                                            return <li key={i} className="mdc-list-item" role="menuitem" tabIndex="0">{d}</li>
                                        })}
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div className="mdc-layout-grid__cell--span-1-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">

                            <div className="menu-filters mdc-menu-anchor">
                                <button id="btn_year" className="mdc-button mdc-button--raised mdc-button--dense mdc-button--compact" disabled>
                                {this.state.query.year}
                                <i className="material-icons mdc-button__icon">arrow_drop_down</i>
                                </button>
                                <div id="menu_year" className="mdc-simple-menu" tabIndex="-1" style={{maxHeight: '300px'}}>
                                    <ul className="mdc-simple-menu__items mdc-list" role="menu" aria-hidden="true">
                                        {this.state.menus.years.map((d, i) => {
                                            return <li key={i} className="mdc-list-item" role="menuitem" tabIndex="0">{d}</li>
                                        })}
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div className="mdc-layout-grid__cell--span-1-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">

                            <div className="menu-filters mdc-menu-anchor">
                                <button id="btn_month" className="mdc-button mdc-button--raised mdc-button--dense mdc-button--compact" disabled>
                                {month}
                                <i className="material-icons mdc-button__icon">arrow_drop_down</i>
                                </button>
                                <div id="menu_month" className="mdc-simple-menu" tabIndex="-1" style={{maxHeight: '300px'}}>
                                    <ul className="mdc-simple-menu__items mdc-list" role="menu" aria-hidden="true">
                                        {this.state.menus.months.map((d, i) => {
                                            return <li key={i} className="mdc-list-item" role="menuitem" tabIndex="0">{d}</li>
                                        })}
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div className="mdc-layout-grid__cell--span-1-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">

                            <div className="menu-filters mdc-menu-anchor">
                                <button id="btn_day" className="mdc-button mdc-button--raised mdc-button--dense mdc-button--compact" disabled>
                                {this.state.query.day}
                                <i className="material-icons mdc-button__icon">arrow_drop_down</i>
                                </button>
                                <div id="menu_day" className="mdc-simple-menu" tabIndex="-1" style={{maxHeight: '300px'}}>
                                    <ul className="mdc-simple-menu__items mdc-list" role="menu" aria-hidden="true">
                                        {this.state.menus.days.map((d, i) => {
                                            return <li key={i} className="mdc-list-item" role="menuitem" tabIndex="0">{d}</li>
                                        })}
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div className="mdc-layout-grid__cell--span-1-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">

                            <div className="menu-filters mdc-menu-anchor">
                                <button id="btn_month2" className="mdc-button mdc-button--raised mdc-button--dense mdc-button--compact" disabled>
                                {month2}
                                <i className="material-icons mdc-button__icon">arrow_drop_down</i>
                                </button>
                                <div id="menu_month2" className="mdc-simple-menu" tabIndex="-1" style={{maxHeight: '300px'}}>
                                    <ul className="mdc-simple-menu__items mdc-list" role="menu" aria-hidden="true">
                                        {this.state.menus.months.map((d, i) => {
                                            return <li key={i} className="mdc-list-item" role="menuitem" tabIndex="0">{d}</li>
                                        })}
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div className="mdc-layout-grid__cell--span-1-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">

                            <div className="menu-filters mdc-menu-anchor">
                                <button id="btn_day2" className="mdc-button mdc-button--raised mdc-button--dense mdc-button--compact" disabled>
                                {this.state.query.day2}
                                <i className="material-icons mdc-button__icon">arrow_drop_down</i>
                                </button>
                                <div id="menu_day2" className="mdc-simple-menu" tabIndex="-1" style={{maxHeight: '300px'}}>
                                    <ul className="mdc-simple-menu__items mdc-list" role="menu" aria-hidden="true">
                                        {this.state.menus.days.map((d, i) => {
                                            return <li key={i} className="mdc-list-item" role="menuitem" tabIndex="0">{d}</li>
                                        })}
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div className="mdc-layout-grid__cell--span-1-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">

                            <button id="btn_filter" 
                            className="mdc-button mdc-button--raised mdc-button--dense mdc-button--compact" 
                            onClick={this.handleFilterClicked} type="button">
                            <i className="material-icons mdc-button__icon">filter_list</i>
                            Go
                            </button>

                        </div>

                    </div>

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-12-desktop mdc-layout-grid__cell--span-12-tablet mdc-layout-grid__cell--span-12-phone">

                            <h4 className="mdc-typography--subheading2">Total Inquiries out of <span>1000</span></h4>

                        </div>

                        {this.state.knobs.map((d, i) => {
                            return <div key={d.id} 
                                    className="mdc-layout-grid__cell--span-3-desktop
                                    mdc-layout-grid__cell--span-4-tablet 
                                    mdc-layout-grid__cell--span-4-phone">
                                <JQueryKnob id={'knobs_' + i} text={d.text} 
                                color={d.color} height="80" width="80" />
                            </div>
                        })}

                    </div>

                    <div className="mdc-layout-grid__inner">
                        <div className="mdc-layout-grid__cell--span-12">&nbsp;</div>
                    </div>

                    <div className="mdc-layout-grid__inner">
                        <div className="mdc-layout-grid__cell--span-12">&nbsp;</div>
                    </div>

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-8-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                            
                            <div className="mdc-elevation--z1">
                                    <C3Chart data={this.state.graph.line.data} 
                                    axis={{x:{type:'category'}}}
                                    zoom={{rescale:true}}
                                    grid={{y:{show:true}}}
                                    padding={{top: 10, bottom: 40}}
                                    colors={this.state.graph.line.colors} />

                                    <ResponsiveContainer width="100%" height={300} >
                                    <LineChart data={[]}>
                                        <XAxis dataKey="month" padding={{left: 30, right: 30}}/>
                                        <YAxis/>
                                        <Tooltip />
                                        <Line type="monotone" dataKey="tc" stroke="#8884d8" />
                                    </LineChart>
                                    </ResponsiveContainer>
                            </div>

                        </div>

                        <div className="mdc-layout-grid__cell--span-4-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">

                            <section className="box">
                                {this.state.counters && 

                                <div className="box-content">
                                    <div className="mdc-list-group">
                                        <h3 className="mdc-list-group__subheader">
                                        Loans and Benefits Application
                                        </h3>
                                        <ul className="mdc-list mdc-list--dense">                               
                                            {this.state.counters.map((d, i) => {

                                                return <li key={i} className="mdc-list-item">
                                                    <i className="mdc-list-item__graphic material-icons" 
                                                    aria-hidden="true">show_charts</i>
                                                    {d.type}
                                                    <span className="mdc-list-item__meta">{d.total}</span>
                                                </li>

                                            })}
                                        </ul>
                                    </div>
                                </div>

                                }
                            </section>

                        </div>

                    </div>

                </div>

            </section>

        )

    }

    //<LineGraph id="line_dashboard" 
    //data={this.state.linedata} 
    //options={this.state.lineopts} />

}

export default withRouter(Dashboard)