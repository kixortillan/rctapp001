import React from 'react'
import {MDCSimpleMenu} from '@material/menu'

class DropdownMenu extends React.Component{

    construct(props) {

        super(props)
        this.state = {

            show: false
            menu: null,

        }

    }

    componentDidMount() {

        const {show, menu} = this.state
        const {id} = this.props

        const menuMore = document.querySelector('#' + id)

        const menu = new MDCSimpleMenu(menuMore)

        const btnMore = document.querySelector('.toolbar-more')
        
        if(btnMore){
            btnMore.addEventListener('click', (event) => {

                if(menu.open){
                    menu.hide()
                }else{
                    menu.show()
                }

            })
        }

        this.setState({ menu: menu })

    }
    
    render() {

        const {show, menu} = this.state
        const {id} = this.props

        if(show) {
            menu.show()
        }

        return (

            <div id={id} className="mdc-menu-anchor" key="toolbar_more">
                <button className="mdc-toolbar__menu-icon mdc-theme--secondary-dark">
                    <span className="material-icons">more_vert</span>
                </button>
                <div className="mdc-simple-menu">
                    <ul className="mdc-simple-menu__items mdc-list" role="menu" aria-hidden="false">
                        <li className="mdc-list-item" role="menuitem" tabIndex="-1" aria-disabled="false">
                            <a href="#">Sign Out</a>
                        </li>
                    </ul>
                </div>
            </div>

        )

    }

}

export default DropdownMenu