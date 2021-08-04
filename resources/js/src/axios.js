// axios
import axios from 'axios'

const baseURL = process.env.MIX_BASE_API

export default axios.create({
  baseURL
  // You can add your headers here
})
