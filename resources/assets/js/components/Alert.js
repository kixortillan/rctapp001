import React from 'react'

class Alert extends React.Component{

    componentDidMount() {

    }
    
    render() {

        const {
            show, type, top,
            bottom, left,
            right, opacity,
            textAlign
        } = this.props

        var displayCss = 'none'

        if(show){
            displayCss = ''
        }

        var bgColor
        var fontColor
        
        if(type === 'error'){
            bgColor = '#F44336'
            fontColor = '#FFFFFF'
        } else if (type === 'warning') {
            bgColor = '#FF9800'
            fontColor = '#000000'
        } else if (type === 'info') {
            bgColor = '#2962FF'
            fontColor = '#FFFFFF'
        } else if (type === 'success') {
            bgColor = '#558B2F'
            fontColor = '#FFFFFF'
        } else {
            bgColor = this.props.bgColor
            fontColor = this.props.fontColor
        }

        return (

            <div style={{
            display: displayCss, 
            textAlign: textAlign,
            transition: 'display 1s ease 0.5s',
            padding: '0.75rem 1rem', top: top,
            bottom: bottom, left: left,
            right: right, backgroundColor: bgColor,
            color: fontColor, opacity: opacity,}}>

                <div>{this.props.text}</div>

            </div>

        )

    }

}

export default Alert