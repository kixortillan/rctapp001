import React from 'react'
import {MDCLinearProgress} from '@material/linear-progress'

class LinearProgress extends React.Component{

    componentDidMount() {

        const progressBar = new MDCLinearProgress(document.querySelector('.mdc-linear-progress'))
        progressBar.open()
    }
    
    render() {

        const {show} = this.props
        let displayCss = 'none'

        if(show){
            displayCss = ''
        }

        return (

            <div style={{display: displayCss}}>

                <div role="progressbar" className="loader mdc-linear-progress mdc-linear-progress--indeterminate" style={{position: 'relative', top: 0, left: 0, zIndex: '101'}}>
                    
                    <div className="mdc-linear-progress__buffering-dots"></div>
                    
                    <div className="mdc-linear-progress__buffer"></div>
                    
                    <div className="mdc-linear-progress__bar mdc-linear-progress__primary-bar">
                        <span className="mdc-linear-progress__bar-inner"></span>
                    </div>
                    
                    <div className="mdc-linear-progress__bar mdc-linear-progress__secondary-bar">
                        <span className="mdc-linear-progress__bar-inner"></span>
                    </div>

                </div>

                <div style={{position: 'fixed', zIndex: '100', height: '100%', width: '100%', backgroundColor: '#FFF', opacity: 0}}>

                </div>

            </div>

        )

    }

}

export default LinearProgress