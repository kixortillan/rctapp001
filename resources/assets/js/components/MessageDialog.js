import React from 'react'
import {MDCDialog} from '@material/dialog'

class MessageDialog extends React.Component{

    constructor(props) {
        
        super(props)
        this.state = {

            dialog: null,
            show: false,

         }

    }

    componentDidMount() {

        let dialog = new MDCDialog(document.getElementById(this.props.id))

        dialog.listen('MDCDialog:accept', () => {
            dialog.close()
            this.props.onClose()
            this.setState({show: false})
        })
        
        this.setState({dialog: dialog})

    }

    componentWillReceiveProps(nextProps) {

        const {show} = this.state

        if(show !== nextProps.show) {
            this.setState({show: nextProps.show})
        }

    }

    render() {
    
        const  {dialog, title, message, show} = this.state

        if(show) {
            dialog.show()
        }
        
        return (

            <aside id={this.props.id}
            className="mdc-dialog"
            role="alertdialog"
            aria-labelledby="my-mdc-dialog-label"
            aria-describedby="my-mdc-dialog-description">
                <div className="mdc-dialog__surface">
                    <header className="mdc-dialog__header">
                        <h2 id="my-mdc-dialog-label" className="mdc-dialog__header__title">
                        {this.props.title}
                        </h2>
                    </header>
                    <section id="my-mdc-dialog-description" className="mdc-dialog__body">
                    {this.props.message}
                    </section>
                    <footer className="mdc-dialog__footer">
                        <button type="button" className="mdc-button mdc-dialog__footer__button mdc-dialog__footer__button--accept">Ok</button>
                    </footer>
                </div>
                <div className="mdc-dialog__backdrop"></div>
            </aside>

        )

    }

}

export default MessageDialog