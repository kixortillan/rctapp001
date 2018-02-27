import React from 'react'
import {
  Redirect,
} from 'react-router-dom'

const REDIRECT_PATH = '/login'

class Logout extends React.Component{

    constructor(props) {
        
        super(props)

        this.state = {

        }

    }


    componentWillMount() {

        this.props.onLogout()

    }
    
    render() {

        return (

            <Redirect to={REDIRECT_PATH + '?m=You have logged out from your account.'} />

        )

    }

}

export default Logout