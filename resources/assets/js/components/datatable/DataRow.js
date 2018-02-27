import React from 'react'

class DataRow extends React.Component{

    componentDidMount() {

    }
    
    render() {
        
        return (

            <tr onClick={this.props.onClick}>
                {this.props.children}
            </tr>

        )

    }

}

export default DataRow