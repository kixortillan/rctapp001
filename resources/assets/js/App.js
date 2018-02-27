import React from 'react'
import {
  BrowserRouter as Router,
  Route,
  Redirect,
  Switch,
} from 'react-router-dom'

import PrimaryNav from './components/PrimaryNav'
import PhoneNav from './components/PhoneNav'
import Toolbar from './components/Toolbar'

import PrivateRoute from './views/auth/PrivateRoute'

import loadLogin from 'bundle-loader?lazy!./views/Login'
import loadLogout from 'bundle-loader?lazy!./views/Logout'
import loadForgotPassword from 'bundle-loader?lazy!./views/auth/ForgotPassword'
import loadResetPassword from 'bundle-loader?lazy!./views/auth/ResetPassword'

import loadDashboard from 'bundle-loader?lazy!./views/Dashboard'
import loadReports from 'bundle-loader?lazy!./views/Reports'
import loadTransactionsReport from 'bundle-loader?lazy!./views/reports/TransactionsReport'
import loadUsers from 'bundle-loader?lazy!./views/Users'
import loadRegister from 'bundle-loader?lazy!./views/auth/Register'
import loadAdmins from 'bundle-loader?lazy!./views/employees/Admins'
import loadEmployees from 'bundle-loader?lazy!./views/employees/Employees'
import loadProfile from 'bundle-loader?lazy!./views/users/Profile'

import Bundle from './utilities/Bundle'
import {hasRole} from './utilities/helpers'

const RouteLogin = (props) => (
    <Bundle load={loadLogin}>
        {(Login) => (Login ? <Route {...props} render={(innerProps) => (<Login {...props} />)} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteLogout = (props) => (
    <Bundle load={loadLogout}>
        {(Logout) => (Logout ? <Route {...props} render={(innerProps) => (<Logout {...props} />)} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteForgotPassword = (props) => (
    <Bundle load={loadForgotPassword}>
        {(ForgotPassword) => (ForgotPassword ? <Route {...props} render={(innerProps) => (<ForgotPassword {...props} />)} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteResetPassword = (props) => (
    <Bundle load={loadResetPassword}>
        {(Comp) => (Comp ? <Route {...props} render={(innerProps) => (<Comp {...props} />)} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteDashboard = (props) => (
    <Bundle load={loadDashboard}>
        {(Dashboard) => (Dashboard ? <PrivateRoute component={Dashboard} {...props} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteReports = (props) => (
    <Bundle load={loadReports}>
        {(Comp) => (Comp ? <PrivateRoute component={Comp} {...props} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteTransactions = (props) => (
    <Bundle load={loadTransactionsReport}>
        {(Comp) => (Comp ? <Route {...props} render={(innerProps) => (<Comp {...props} />)} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteUsers = (props) => (
    <Bundle load={loadUsers}>
        {(Comp) => (Comp ? <PrivateRoute component={Comp} {...props} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteRegister = (props) => (
    <Bundle load={loadRegister}>
        {(Comp) => (Comp ? <PrivateRoute component={Comp} {...props} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteAdmins = (props) => (
    <Bundle load={loadAdmins}>
        {(Comp) => (Comp ? <PrivateRoute component={Comp} {...props} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteEmployees = (props) => (
    <Bundle load={loadEmployees}>
        {(Comp) => (Comp ? <PrivateRoute component={Comp} {...props} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

const RouteProfile = (props) => (
    <Bundle load={loadProfile}>
        {(Comp) => (Comp ? <PrivateRoute component={Comp} {...props} /> : <LinearProgress show={true} /> )}
    </Bundle>
)

class App extends React.Component{

    constructor(props) {

        super(props)

        this.state = {

            isAuthenticated: false,

            isNavCollapsed: false,

            isPhoneNavCollapsed: false,
            
            module: '',
            
            user: {},

            roles: [],

        }

        this.login = this.login.bind(this)
        this.logout = this.logout.bind(this)
        this.handleToggleNav = this.handleToggleNav.bind(this)
        this.handleTogglePhoneNav = this.handleTogglePhoneNav.bind(this)
        this.changeModule = this.changeModule.bind(this)

    }

    changeModule(module) {

        this.setState({module: module})

    }

    login(credentials) {

        localStorage.setItem('access_token', credentials.access_token)
        localStorage.setItem('refresh_token', credentials.refresh_token)
        localStorage.setItem('expires_in', credentials.expires_in)
        localStorage.setObject('filters', credentials.filters)
        localStorage.setObject('user', credentials.user)
        localStorage.setObject('roles', credentials.roles)

        this.setState({isAuthenticated: true})

    }

    logout(callback) {

        this.setState({isAuthenticated: false})

        localStorage.removeItem('access_token')
        localStorage.removeItem('refresh_token')
        localStorage.removeItem('expires_in')
        localStorage.removeItem('user')
        localStorage.removeItem('roles')

    }

    handleToggleNav(event) {

        const {isNavCollapsed} = this.state
        this.setState({ isNavCollapsed: !isNavCollapsed })
        localStorage.setItem('nav_collapse', !isNavCollapsed)

    }

    handleTogglePhoneNav(event) {

        const {isPhoneNavCollapsed} = this.state
        this.setState({ isPhoneNavCollapsed: !isPhoneNavCollapsed })

    }

    componentWillMount() {

        const isLoggedIn = localStorage.getItem('access_token') !== null ? true : false
        const isNavCollapsed = localStorage.getItem('nav_collapse') 
                                ? localStorage.getItem('nav_collapse') : false
        const user = localStorage.getObject('user')
        const roles = localStorage.getObject('roles')

        this.setState({
            isAuthenticated: isLoggedIn, 
            user: user,
            roles: roles,
            isNavCollapsed: isNavCollapsed
        })

    }
    
    componentDidMount() {

        

    }

    render() {

        const {isAuthenticated, isNavCollapsed, isPhoneNavCollapsed, module, user, roles} = this.state
        const {cookies} = this.props

        return (

            <Router>

                <div className="main">

                    {isAuthenticated && <PrimaryNav collapse={isNavCollapsed} 
                                        onToggle={this.handleToggleNav} />}

                    {isAuthenticated && <PhoneNav collapse={isPhoneNavCollapsed} />}

                    <main className="content">

                        {isAuthenticated && 

                        <Toolbar>
                            <header className="header mdc-toolbar 
                            mdc-elevation--z1 mdc-theme--background">
                
                                <div className="mdc-toolbar__row">

                                    <section className="mdc-toolbar__section 
                                    mdc-toolbar__section--align-start">
                                        <button 
                                        onClick={this.handleTogglePhoneNav} 
                                        className="hide-on-desktop 
                                        hide-on-tablet
                                        material-icons 
                                        mdc-toolbar__menu-icon
                                        mdc-theme--secondary-dark"
                                        type="button">
                                        menu
                                        </button>
                                        <span key="module" 
                                        className="mdc-toolbar__title 
                                        mdc-theme--secondary-dark">
                                        {module}
                                        </span>
                                    </section>

                                </div>

                            </header>
                        </Toolbar>}

                        <Switch>

                            {/*Routes for Public Module*/}
                            <RouteLogin exact path="/" isAuthenticated={isAuthenticated} 
                            onLogin={this.login} />
                            <RouteLogin path="/login" isAuthenticated={isAuthenticated} 
                            onLogin={this.login} />
                            <RouteLogout path="/logout" onLogout={this.logout} />
                            <RouteForgotPassword path="/forgot/password" />
                            <RouteResetPassword path="/reset/password" />

                            {/*Routes for Reports Module*/}
                            <RouteTransactions path="/reports/transactions" />

                            {/*Routes for Main Module*/}
                            <RouteDashboard path="/dashboard" onChangeModule={this.changeModule} 
                            onChangeModule={this.changeModule} isAuthenticated={isAuthenticated} />
                            <RouteReports path="/reports" onChangeModule={this.changeModule} 
                            isAuthenticated={isAuthenticated} />
                            <RouteUsers path="/users" onChangeModule={this.changeModule} 
                            isAuthenticated={isAuthenticated} />
                            <RouteProfile path="/profile/:username" onChangeModule={this.changeModule} 
                            isAuthenticated={isAuthenticated} />
                            <RouteRegister path="/register" onChangeModule={this.changeModule} 
                            isAuthenticated={isAuthenticated} />


                        </Switch>

                    </main>

                </div>     

            </Router>       

        )

    }

}

export default App