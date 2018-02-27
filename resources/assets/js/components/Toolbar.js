import React from 'react'

class Toolbar extends React.Component{
    
    render() {

        return (

            <header className="header mdc-toolbar mdc-elevation--z1 mdc-theme--background">
                
                <div className="mdc-toolbar__row">
                    
                    {this.props.children}

                </div>

            </header>

        )

    }

}

export default Toolbar