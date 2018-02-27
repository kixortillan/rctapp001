import React from 'react'
import $ from 'jquery'
import Knob from 'jquery-knob/dist/jquery.knob.min'

class JQueryKnob extends React.Component{
    
    constructor(props) {
        super(props)
    }

    render() {

        return (

            <div className="knob-ui">
                <input id={this.props.id} className="knob" data-fgcolor={this.props.color} data-height={this.props.height} data-width={this.props.width} value={this.props.value} type="text"/>   
                <div>
                    <span>{this.props.text}</span>
                </div>
            </div>
        )

    }

    componentDidMount() {

        $(".knob").knob({
            readOnly: true,
            format: (val) => {
                return val + '%'
            }
        })

    }

}

export default JQueryKnob