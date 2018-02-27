import React from 'react'
import {
    Route,
    Redirect
} from 'react-router-dom'

const REDIRECT_PATH = '/login'

const PrivateRoute = ({ component: Component, isAuthenticated: isAuthenticated, onChangeModule: onChangeModule, ...rest }) => {
    
    return (
        <Route {...rest} 
            render={
                (props) => {
                    return isAuthenticated 
                    ? (<Component isAuthenticated={isAuthenticated} 
                        onChangeModule={onChangeModule} {...props}/>) 
                    : (<Redirect to={{
                        pathname: REDIRECT_PATH,
                        search: '?m=Your session has expired.',
                        state: { from: props.location }}}/>)
                }
            } />
    )

}

export default PrivateRoute