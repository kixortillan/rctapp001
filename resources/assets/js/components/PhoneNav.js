import React from 'react'
import {NavLink, withRouter} from 'react-router-dom'

import routes from '../config/routes'

class PhoneNav extends React.Component{

    constructor(props) {

        super(props)
        
        this.state = {

            user: {},

        }

        this.getClassDrawer = this.getClassDrawer.bind(this)

    }

    componentDidMount() {

        const user = localStorage.getObject('user')
        
        this.setState({
            user: user
        })

    }

    getClassDrawer(isCollapsed) {

        if(isCollapsed) {
            
            return ' mdc-drawer--open'

        }

        return ''
    }

    render() {

        const {user} = this.state
        const {collapse} = this.props

        return (

            <aside className={'hide-on-tablet hide-on-desktop mdc-drawer--persistent mdc-drawer mdc-theme--primary-bg' 
            + this.getClassDrawer(collapse)}>

                <nav className="mdc-drawer__drawer mdc-theme--primary-bg">

                    <div className="mdc-drawer__toolbar-spacer" style={{padding: '8px 5px'}}>
                        <a href="#" className="logo" style={{margin: 'auto'}}>
                            <img src="/images/logo-sss-large.png" height="32" />
                        </a>
                    </div>

                    <div className="mdc-drawer__toolbar-spacer" style={{padding: '8px 5px'}}>
                        <img className="user-avatar" src={user.avatar} width="40" />
                        <a href={"/profile/@" + user.username} className="link-name mdc-theme--primary-light" 
                        style={{marginLeft: '10px', width: '100%', textAlign: 'left'}}>
                        {(user.full_name !== null && user.full_name !== '') ? user.full_name : 'user' + user.id}
                        </a>
                    </div>

                    <div className="mdc-list-group">
                        <nav className="mdc-drawer__content mdc-list mdc-list--dense">
                            {routes.map((d, i) => {

                                return <NavLink key={d.path} to={d.path} 
                                        activeClassName="nav-active" 
                                        className="mdc-list-item mdc-list-item--selected 
                                        mdc-theme--primary-light">
                                    <i className="material-icons mdc-list-item__graphic 
                                    mdc-theme--primary-light" aria-hidden="true">
                                    {d.icon}
                                    </i>
                                    {d.text}
                                </NavLink>

                            })}
                        </nav>
                    </div>

                </nav>
            </aside>

        )

        

    }

}

export default withRouter(PhoneNav)