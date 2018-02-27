import React from 'react'
import styles from './material-datatable.css'

class DataTable extends React.Component{

    constructor(props){

        super(props)
        this.handleOnClickPrevious = this.handleOnClickPrevious.bind(this)
        this.handleOnClickNext = this.handleOnClickNext.bind(this)

    }

    handleOnClickPrevious(event) {

        this.props.onClickPrevious({
            page: this.props.page < 1 ? 1 : this.props.page - 1,
            per_page: this.props.perPage,
            search: this.props.search,
            sort_by: this.props.sortBy,
            sort: this.props.sort,
        })

    }

    handleOnClickNext(event) {

        this.props.onClickPrevious({
            page: this.props.page > this.props.lastPage ? this.props.lastPage : this.props.page + 1,
            per_page: this.props.perPage,
            search: this.props.search,
            sort_by: this.props.sortBy,
            sort: this.props.sort,
        })

    }

    componentWillMount() {
            

    }
    
    render() {

        const {
            headers, body, paginate,
            page, perPage, total,
            lastPage, children
        } = this.props

        const start = ((page - 1) * perPage) + 1
        const end = (((page - 1) * perPage) + perPage) > total 
                        ? total : ((page - 1) * perPage) + perPage

        return (

            <table className="data-table mdl-data-table mdl-data-table--selectable">
                
                <thead className="mdc-theme--primary-bg">
                    <tr>
                        {headers && headers.map(function(d, i){

                            var cName = 'mdl-data-table__cell--non-numeric'

                            if(d.type === 'number') {
                                cName = ''
                            }

                            return <th key={'th_' + i} 
                            className={'mdc-theme--primary-light' + ' ' + cName}>
                            {d.header}
                            </th>

                        })}
                    </tr>
                </thead>

                <tbody>
                    {
                        children.length > 0 ?

                        children :

                        <tr>
                            <td colSpan={headers && headers.length} style={{textAlign: 'center'}}>No records found.</td>
                        </tr>
                    }
                </tbody>
                {

                    (paginate && !isNaN(page) && total > 0) ?
                    
                    <tfoot>
                        <tr>
                            <td className="" colSpan={headers && headers.length}>

                                <span className="md-table-pagination--label">
                                {start}-{end} of {total}
                                </span>

                                <button onClick={this.handleOnClickPrevious} 
                                className="md-btn md-btn--icon md-pointer--hover" 
                                disabled={page <= 1}>
                                    <i className="material-icons">keyboard_arrow_left</i>
                                </button>

                                <button onClick={this.handleOnClickNext} 
                                className="md-btn md-btn--icon md-pointer--hover md-inline-block" 
                                disabled={page >= lastPage}>
                                    <i className="material-icons">keyboard_arrow_right</i>
                                </button>

                            </td>
                        </tr>
                    </tfoot>

                    :

                    null
                }
            </table>

        )

    }

}

export default DataTable