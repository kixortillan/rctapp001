import React from 'react'
import {
    withRouter,
    Route,
} from 'react-router-dom'

class Reports extends React.Component {

    constructor(props) {

        super(props)

        //data Reports manages
        this.state = {

        }

    }

    componentDidMount() {

        this.props.onChangeModule('Reports')

    }
    
    render() {

        return (

            <section>

                <div className="mdc-layout-grid">

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-12">

                            <div className="mdc-grid-list mdc-grid-list--with-icon-align-start">
                                <ul className="reports-list mdc-grid-list__tiles">
                                    <li className="mdc-grid-tile" onClick={() => {this.props.history.push('/reports/transactions')}}>
                                        <div className="mdc-grid-tile__primary">
                                        </div>
                                        <span className="mdc-grid-tile__secondary">
                                            <i className="material-icons mdc-grid-tile__icon">assignment</i>
                                            <span className="mdc-grid-tile__title">Transactions</span>
                                        </span>
                                    </li>
                                </ul>
                            </div>

                        </div>

                    </div>

                </div>

            </section>

        )

    }

}

export default withRouter(Reports)