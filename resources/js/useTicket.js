export const showTicketAvailable = (queues, ticketType) => {
  if (queues[ticketType] === undefined || queues[ticketType]?.available === null) {
    return '-'
  }

  return `${ticketType}${queues[ticketType].available}`
}

export const showTicketWaiting = (queues, ticketType) => {
  if (queues[ticketType] === undefined || queues[ticketType]?.waiting === null) {
    return '-'
  }

  return `${ticketType}${queues[ticketType].waiting}`
}

export const showDetails = (queues, ticketType) => {
  if (queues[ticketType] === undefined || queues[ticketType]?.details === null) {
    return []
  }

  return queues[ticketType].details
}

export const noDetails = (queues, ticketType) => {
  return queues[ticketType] === undefined || queues[ticketType]?.details === null
}

export const noNext = (queues, ticketType) => {
  if (queues[ticketType] === undefined || queues[ticketType]?.waiting === null) {
    return true
  }

  return queues[ticketType].waiting === queues[ticketType].available || queues[ticketType].diff === 0
}

export const isMine = (queues, ticketType, ticketNumber) => {
  if (queues[ticketType] === undefined || queues[ticketType]?.available === null) {
    return false
  }

  return queues[ticketType].available === ticketNumber
}

export const isBefore = (queues, ticketType, ticketNumber) => {
  if (queues[ticketType] === undefined || queues[ticketType]?.available === null) {
    return true
  }

  return queues[ticketType].available < ticketNumber
}

export const lastCallTime = (queues, ticketType) => {
  if (queues[ticketType] === undefined || queues[ticketType]?.details === null) {
    return '-'
  }

  return queues[ticketType].details[queues[ticketType].details.length - 1].updated_at
}
