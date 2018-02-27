import React from 'react'

class CardCounter extends React.Component{
    
    render() {

        const {number, text, icon, bgColor, fontColor, width} = this.props

        return (

            <div className="mdc-elevation--z1" style={{display: 'block',backgroundColor: bgColor, width: width}}>

                <div style={{display: 'flex'}}>
                    
                    <section style={{flexGrow: 1, padding: '1.25rem 0', textAlign: 'center'}}>
                        <span className="mdc-typography--title material-icons" style={{fontSize: '3rem', color: fontColor}}>{icon}</span>
                    </section>

                    <section style={{flexGrow: 1, padding: '1.25rem 0'}}>
                        <span className="mdc-typography--title" style={{display: 'block', fontSize: '1.25rem', color: fontColor}}>{number}</span>
                        <span className="mdc-typography--subheading1" style={{display: 'block', fontSize: '0.75rem', color: fontColor}}>{text}</span>
                    </section>

                </div>


            </div>

        )

    }

}

export default CardCounter