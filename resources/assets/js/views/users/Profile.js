import React from 'react'
import {withRouter} from 'react-router-dom'
import QueryString from 'query-string'

import {MDCTabBar} from '@material/tabs'

class Profile extends React.Component{
    
    constructor(props) {

        super(props)

        //data users/Profile manages
        this.state = {

            //query string for calling api's
            query: {},

            user: {},

            roles: {},

            //toggle popup dialog
            show_message: false,

            //message to show at dialog
            message: '',

        }//end of state

    }

    componentWillMount() {
            
        const {match} = this.props

        axios.get('/api/users/user/' + match.params.username)
            .then((resp) => {

                console.log(resp.data)
                
                var data = resp.data.data

                this.setState({
                    user: data.user.data,
                    roles: data.roles.data,
                })


            }).catch((err) => {

                console.log(err)

            });

    }

    componentDidMount() {

        var profTab = new MDCTabBar(document.querySelector('#profile_tab'));
        
        profTab.tabs.forEach(function(tab) {
            tab.preventDefaultOnClick = true
        })
    }

    render() {

        const {match} = this.props
        const {user, roles} = this.state

        return (

            <section>

                <div className="mdc-layout-grid">

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-12">

                            <div className="mdc-elevation--z1" style={{display: 'flex'}}>

                                <section style={{flexGrow: '1', width: '100px'}}>
                                    
                                    <div>
                                        <img src={user.avatar} />
                                    </div>

                                    <div>
                                        <div>{user.full_name}</div>
                                        <div>{'@' + user.username}</div>
                                        <div>{roles.length > 0 && roles[0].role}</div>
                                    </div>

                                    <div>
                                        <div>Email: {user.email}</div>
                                        <div>Mobile: {user.mobile_number}</div>
                                        <div>Address: {user.address}</div>
                                        <span></span>
                                    </div>

                                </section>

                                <section style={{flexGrow: '2'}}>
                                    
                                    <section>
                                        <nav id="profile_tab" className="mdc-tab-bar" style={{margin: 0}}>
                                    
                                                <a href="#" className="mdc-tab">
                                                Activities
                                                </a>
                                                <a href="#" className="mdc-tab">
                                                Roles
                                                </a>
                                            
                                            <span className="mdc-tab-bar__indicator"></span>
                                        </nav>
                                    </section>
                                    <section className="panels">
                                        <div className="panel active">
                                            Panel 1
                                        </div>
                                        <div className="panel">
                                            Panel 2
                                        </div>
                                    </section>

                                </section>

                            </div>

                        </div>

                    </div>

                </div>

            </section>

        )

    }

}

export default withRouter(Profile)