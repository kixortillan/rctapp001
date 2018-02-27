import React from 'react'
import {
  Redirect,
  NavLink,
} from 'react-router-dom'
import {
    withFormik, 
    Form,
} from 'formik'
import Yup from 'yup'
import QueryString from 'query-string'
import {MDCTextField} from '@material/textfield'

import Toolbar from '../components/Toolbar'
import Alert from '../components/Alert'

const DASHBOARD_PATH = '/dashboard'

class Login extends React.Component{

    constructor(props) {
        
        super(props)

        this.state = {

            form: {

                username: '',
                password: '',

            },

            alert: {
                show: false,
                type: '',
                text: '',
            },

            //
            redirectToReferrer: false,

        }

        this.handleOnSubmitted = this.handleOnSubmitted.bind(this)

    }

    handleOnSubmitted(response) {
        
        if(response.status && response.status == 200){
            
            this.props.onLogin(response.data)

        } else if (response.response && response.response.status == 422) {

            this.setState({
                alert: {
                    show: true,
                    type: 'error',
                    text: response.response.data.error,
                }
            })

        } else {

            this.setState({
                alert: {
                    show: true,
                    type: 'error',
                    text: 'An error has occurred. Please contact system administrator.',
                }
            })

        }

    }

    componentDidMount() {
    
        const txtUsername = document.querySelector('form input')

        if(txtUsername !== null){
            txtUsername.focus()
        }

        var qs = QueryString.parse(location.search)

        if(qs.m) {
            this.setState({
                alert: {
                    show: true,
                    type: 'success',
                    text: qs.m,
                }
            })
        }

    }
    
    render() {

        const { from } = this.props.location.state || { from: { pathname: DASHBOARD_PATH } }
        
        //allow redirect to protected route if authenticated
        if(this.props.isAuthenticated) {

            return (

                <Redirect to={from} />

            )

        }

        return (

            <div>

                <section style={{paddingTop: '64px'}}>

                        <div className="mdc-layout-grid">

                            <div className="mdc-layout-grid__inner">

                                <div className="mdc-layout-grid__cell--span-12">

                                    <img src="images/logo-sss-large.png" 
                                    alt="SSS Logo" 
                                    style={{display: 'block', margin: '0 auto'}}/>
                                    <div className="login-logo">SSS Web App</div>

                                </div>

                            </div>

                            <div className="mdc-layout-grid__inner">

                                <div className="mdc-layout-grid__cell--span-5-desktop
                                mdc-layout-grid__cell--span-2-tablet
                                mdc-layout-grid__cell--span-4-phone" />

                                <div className="mdc-layout-grid__cell--span-2-desktop 
                                mdc-layout-grid__cell--span-4-tablet
                                mdc-layout-grid__cell--span-4-phone">

                                    <Alert show={this.state.alert.show} 
                                    type={this.state.alert.type}
                                    text={this.state.alert.text}
                                    textAlign="center" />

                                    <section className="box-content">

                                        <FormikForm form={this.state.form} onSubmitted={this.handleOnSubmitted} />

                                    </section>

                                </div>

                                <div className="mdc-layout-grid__cell--span-5-desktop
                                mdc-layout-grid__cell--span-2-tablet
                                mdc-layout-grid__cell--span-4-phone" />

                                <div className="mdc-layout-grid__cell--span-5-desktop
                                mdc-layout-grid__cell--span-2-tablet
                                mdc-layout-grid__cell--span-4-phone" />

                                <div className="mdc-layout-grid__cell--span-2-desktop 
                                mdc-layout-grid__cell--span-4-tablet
                                mdc-layout-grid__cell--span-4-phone">

                                    <NavLink to="/forgot/password" 
                                    style={{padding: '11px', display: 'block', textAlign: 'right'}}>
                                    Forgot Password?
                                    </NavLink>

                                </div>

                            </div>

                        </div>

                </section>

            </div>            

        )

    }

}

const LoginForm = (props) => {

    const {
        values,
        touched,
        errors,
        dirty,
        isSubmitting,
        handleChange,
        setFieldValue,
        handleBlur,
        handleSubmit,
        handleReset,
    } = props

    return (

        <Form onSubmit={handleSubmit}>

            <div className="mdc-text-field" style={{width: '100%'}}>
                <input
                onChange={handleChange}
                value={values.username}  
                id="username" 
                name="username" 
                className="mdc-text-field__input" 
                type="text" 
                placeholder="Username" />
                <div className="mdc-text-field__bottom-line"></div>
            </div>
            
            {touched.username && errors.username && 
            <p className="mdc-text-field-helper-text 
            mdc-text-field-helper-text--validation-msg form-error" 
            aria-live="polite">
            {errors.username}
            </p>}

            <div className="mdc-text-field" style={{width: '100%'}}>
                <input 
                onChange={handleChange}
                value={values.password} 
                id="password" 
                name="password" 
                className="mdc-text-field__input" 
                type="password" 
                placeholder="Password" />
                <div className="mdc-text-field__bottom-line"></div>
            </div>
            
            {touched.password && errors.password && 
            <p className="mdc-text-field-helper-text 
            mdc-text-field-helper-text--validation-msg form-error" 
            aria-live="polite">
            {errors.password}
            </p>}

            <button 
            className="mdc-button mdc-button--raised" 
            style={{width: '100%'}}
            disabled={isSubmitting}>
            Sign in
            </button>

        </Form>

    )

}

const FormikForm = withFormik({

    mapPropsToValues: (props) => ({ 

        username: props.form.username,
        password: props.form.password,

    }),

    validationSchema: Yup.object().shape({

        username: Yup.string()
            .required('This field is required.')
            .max(150, 'Must not exceed 150 characters.'),

        password: Yup.string()
            .required('This field is required.')
            .min(8, 'Must not be less than 8 characters.')
            .max(150, 'Must not exceed 150 characters.'),

    }),

    handleSubmit: (values, { props, setSubmitting }) => {

        setSubmitting(true)

        axios.post('/api/login', values).then((resp) => {
            
            props.onSubmitted(resp)

        }).catch((err) => {

            props.onSubmitted(err)

        }).then(() => {

            setSubmitting(false)

        })

    },

})(LoginForm)

export default Login