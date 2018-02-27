import React from 'react'

class DataCell extends React.Component{

    componentDidMount() {

    }
    
    render() {
        
        const {data, type, ...rest} = this.props

        if(type === 'button'){
            return (

                <td {...rest}>
                    <button></button>
                </td>

            )
        }
        
        if(type === 'image'){
            return (

                <td {...rest}>
                    <img src={data} />
                </td>

            )
        }

        if(type === 'number'){
            return (

                <td {...rest}>
                {data}
                </td>

            )
        }

        return (

            <td className="mdl-data-table__cell--non-numeric" {...rest}>{data}</td>

        )

    }

}

export default DataCell