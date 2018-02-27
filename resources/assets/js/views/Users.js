import React from 'react'
import {
    NavLink,
    withRouter,
} from 'react-router-dom'
import QueryString from 'query-string'
import {MDCTabBar} from '@material/tabs'

import MessageDialog from '../components/MessageDialog'

import PrivateRoute from './auth/PrivateRoute'
import Admins from './employees/Admins'
import Employees from './employees/Employees'

import {hasRole} from '../utilities/helpers'

class Users extends React.Component{
    
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

            roles: [],

        }//end of state


        this.handleRedirectToRegister = this.handleRedirectToRegister.bind(this)
    }

    handleRedirectToRegister(event) {

        const {history} = this.props
        history.push('/register')

    }

    componentWillMount() {
        
        const roles = localStorage.getObject('roles')
        this.setState({ roles: roles })

    }

    componentDidMount() {

        const dynamicTabBar = new MDCTabBar(document.querySelector('#tab_users'));

        this.props.onChangeModule('Users')

        const {roles} = this.state
        const {history} = this.props

        if(hasRole(roles, ['Super User', 'Admin'])){
            history.push('/users/admins')
        }

    }

    render() {

        const {isAuthenticated} = this.props
        const {roles} = this.state
        
        return (

            <section>

                <div className="mdc-layout-grid">

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-12">

                            <button onClick={this.handleRedirectToRegister} 
                            className="mdc-button mdc-button--raised mdc-button--dense 
                            mdc-button--compact pull-right" 
                            aria-label="Add User">
                                <i className="material-icons mdc-button__icon">
                                person_add
                                </i>
                                New User
                            </button>

                            <nav id="tab_users" className="mdc-tab-bar" style={{margin: 0}}>
                                
                                {hasRole(roles, ['Super User', 'Admin']) && 
                                    <NavLink to="/users/admins" className="mdc-tab">
                                    Admins
                                    </NavLink>}
                                    <NavLink to="/users/employees" className="mdc-tab">
                                        Employees
                                    </NavLink>
                                
                                <span className="mdc-tab-bar__indicator"></span>
                            </nav>

                            {hasRole(roles, ['Super User', 'Admin']) && 
                                <PrivateRoute path="/users/admins" component={Admins} isAuthenticated={isAuthenticated} />}
                            <PrivateRoute path="/users/employees" component={Employees} isAuthenticated={isAuthenticated} />
                            
                        </div>

                    </div>

                </div>

            </section>

        )

    }

}

export default withRouter(Users)