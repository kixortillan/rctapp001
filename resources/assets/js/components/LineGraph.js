import React from 'react'
import ChartJs from 'chart.js'

class LineGraph extends React.Component{
    
    constructor(props) {
        super(props)
    }

    handleChangeValue() {



    }

    componentDidMount() {

        const ctx = document.getElementById(this.props.id);

        const chart = new Chart(ctx, {
            
            type: 'line',
            data: this.props.data,
            options: this.props.state

        });

    }

    render() {

        return (

            <canvas id={this.props.id} width={this.props.width} height={this.props.height}></canvas>

        )

    }

}

export default LineGraph