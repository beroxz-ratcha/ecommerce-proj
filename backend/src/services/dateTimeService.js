import dayjs from 'dayjs';

const formatDateTime = (dateString) => {
  return dayjs(dateString).format('DD/MM/YYYY HH:mm:ss');
};

const formatDate = (dateString) => {
  return dayjs(dateString).format('DD/MM/YYYY');
};

const formatDateTimeStr = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleString('en-GB');
};

export default {
  formatDateTime,
  formatDate,
};
