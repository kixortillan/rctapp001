import React from 'react'
import {
    withRouter,
    Redirect,
} from 'react-router-dom'
import QueryString from 'query-string'

import DataTable from '../../components/datatable/DataTable'
import DataRow from '../../components/datatable/DataRow'
import DataCell from '../../components/datatable/DataCell'

class Employees extends React.Component{
    
    constructor(props) {

        super(props)

        //data employee/Employees manages
        this.state = {

            //query string for calling api's
            query: {},

            //toggle popup dialog
            show_message: false,

            //message to show at dialog
            message: '',

            //
            employees: [],

            to_render: [],

            //
            pagination: {},

        }//end of state


        this.bindToInstance()
    }

    bindToInstance() {

        this.loadDataTable = this.loadDataTable.bind(this)
        this.handleOnClickNext = this.handleOnClickNext.bind(this)
        this.handleOnClickPrevious = this.handleOnClickPrevious.bind(this)
        this.handleViewDetails = this.handleViewDetails.bind(this)

    }

    handleOnClickPrevious(query) {

        this.loadDataTable(query)

    }

    handleOnClickNext(query) {

        this.loadDataTable(query)

    }

    handleViewDetails(id){
    
        const {history} = this.props

        history.push('users/user/' + id)

    }

    loadDataTable(query = {}) {

        axios.get('/api/users/employees?' + QueryString.stringify(query))
        .then((resp) => {

            //console.log(resp)

            this.setState({
                employees: resp.data.data,
                pagination: resp.data.meta.pagination,
            })
            
        })
        .catch((err) => {

            console.log(err)

        })

    }

    componentWillMount() {
            
        this.loadDataTable()

    }

    componentDidMount() {


    }

    render() {

        const {employees, pagination} = this.state

        const headers = [
            {header: '', type: 'text'},
            {header: 'NAME', type: 'text'},
            {header: 'ROLE', type: 'text'},
            {header: 'EMAIL', type: 'text'},
            {header: 'MOBILE NUMBER', type: 'text'},
            {header: 'DATE/TIME', type: 'text'},
        ]

        const rows = employees.map((d, i) => {

            return <DataRow onClick={() => { this.handleViewDetails(d.id) }} key={i}>
                <DataCell type="image" data={d.avatar} />
                <DataCell type="text" data={d.full_name} className="mdl-data-table__cell--non-numeric" />
                <DataCell type="text" data={d.roles.data[0].role} className="mdl-data-table__cell--non-numeric" />
                <DataCell type="text" data={d.email} className="mdl-data-table__cell--non-numeric" />
                <DataCell type="text" data={d.mobile_number} className="mdl-data-table__cell--non-numeric" />
                <DataCell type="text" data={d.date_registered} className="mdl-data-table__cell--non-numeric" />
            </DataRow>

        })

        return (

            <section>

                <div className="mdc-layout-grid">

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-12">

                            <DataTable 
                            headers={headers} 
                            page={pagination.current_page}
                            lastPage={pagination.total_pages}
                            perPage={pagination.per_page}
                            total={pagination.total} 
                            paginate={true}
                            onClickNext={this.handleOnClickNext}
                            onClickPrevious={this.handleOnClickPrevious}>
                                {rows}
                            </DataTable>

                        </div>

                    </div>

                </div>

            </section>

        )

    }

}

export default withRouter(Employees)